<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Media;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;

class UploadController extends Controller
{
    private $imageFolder = '';
    private $largeImageFolder = '';
    private $mediumImageFolder = '';
    private $thumbnailImageFolder = '';
    public $cropSizes = [];
    private $cdnUrl = '';

    private $ratio1x1 = [
        'L' => ['w' => 900, 'h' => 900],
        'M' => ['w' => 600, 'h' => 600],
        'S' => ['w' => 300, 'h' => 300],
    ];

    private $ratio2x3 = [
        'L' => ['w' => 900, 'h' => 1350],
        'M' => ['w' => 600, 'h' => 900],
        'S' => ['w' => 300, 'h' => 450],
    ];

    private $ratio16x9 = [
        'L' => ['w' => 1024, 'h' => 576],
        //'L' => ['w' => 800, 'h' => 450],
        'M' => ['w' => 560, 'h' => 315],
        'S' => ['w' => 320, 'h' => 180],
    ];

    //Best for facebook
    private $ratio191x100 = [
        'L' => ['w' => 764, 'h' => 400],
        'M' => ['w' => 573, 'h' => 300],
        'S' => ['w' => 382, 'h' => 200],
    ];

    private $ratio8x5 = [
        'L' => ['w' => 1000, 'h' => 625],
        'M' => ['w' => 800, 'h' => 500],
        'S' => ['w' => 400, 'h' => 250],
    ];

    public function __construct()
    {
        $this->imageFolder = '/images' . date("/Y/m/d/");
        $this->largeImageFolder = '/images/large' . date("/Y/m/d/");
        $this->mediumImageFolder = '/images/medium' . date("/Y/m/d/");
        $this->thumbnailImageFolder = '/images/small' . date("/Y/m/d/");
        $this->cdnUrl = url('/storage/');
        $this->cropSizes = ['1x1' => 'ratio1x1', '4x3' => 'ratio4x3', '3x2' => 'ratio3x2', '16x9' => 'ratio16x9', '191x100' => 'ratio191x100', '2x3' => 'ratio2x3', '8x5' => 'ratio8x5'];
    }

    public function image(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/');
        }

        $response = ['status' => false, 'task' => 'upload', 'errors' => []];
        $messages = [
            'attachment.mimes' => __('Hệ thống chỉ chấp nhận tệp tin có định dạng: jpeg,bmp,png,gif,webp,svg'),
            'attachment.max' => __('Hệ thống chỉ chấp nhận tệp tin nhỏ hơn 4Mb')
        ];

        //Don't use ->validate() to call ->errors() for ajax
        $validator = Validator::make($request->all(), [
            'attachment' => 'mimes:jpeg,bmp,png,gif,webp,svg|max:4096',
        ], $messages);

        if ($validator->fails()) {
            $response['errors'] = $validator->errors()->get('attachment');
            return response()->json($response);
        }

        $useOriginal = $request->useOriginal;
        if ($request->hasfile('attachment')) {
            $attachment = $request->file('attachment');
            $originalExtension = $request->file('attachment')->getClientOriginalExtension();
            $convertExtension = !in_array($originalExtension, ['gif']) ? 'webp' : $originalExtension;
            $fileName = Str::slug(pathinfo($request->attachment->getClientOriginalName(), PATHINFO_FILENAME)) . ".$convertExtension";
            if (Storage::disk('public')->has('images' . date("/Y/m/d/") . $fileName)) {
                $fileName = Str::slug(pathinfo($request->attachment->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . ".$convertExtension";
            }

            if ($originalExtension == 'gif') {
                copy($request->file('attachment')->getRealPath(), storage_path('app/public/' . 'images' . date("/Y/m/d/") . $fileName));
                $response['status'] = true;
            } else {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($attachment);
                $image = $image->encode(new WebpEncoder(quality: 90));

                if (Storage::disk('public')->put('images' . date("/Y/m/d/") . $fileName, (string)$image, 'public')) {
                    $ratio = !empty($request->ratio) ? $request->ratio : '191x100';
                    $results = $this->autoCrop($attachment, $ratio);
                    if (!empty($results['large_images'])) $response['large_images'][] = $results['large_images'];
                    if (!empty($results['medium_images'])) $response['medium_images'][] = $results['medium_images'];
                    if (!empty($results['thumbnail_images'])) $response['thumbnail_images'][] = $results['thumbnail_images'];
                    $response['status'] = true;
                }
            }

            if ($response['status'] == true) {
                $response['images'][] = '/images' . date("/Y/m/d/") . $fileName;
                $media = [
                    'user_id' => Auth::check() ? Auth::user()->id : 0,
                    'path' => !empty($results['large_images']) && !$useOriginal ? $results['large_images'] : '/images' . date("/Y/m/d/") . $fileName,
                    'name' => $fileName,
                    'folder' => '',
                    'type' => 0,
                    'favourite' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('media')->insert($media);
            }
        }

        exit(json_encode($response));
    }

    public function multiple(Request $request)
    {
        $ratio = !empty($request->ratio) ? $request->ratio : '191x100';
        $response = ['status' => false, 'task' => 'upload/multiple', 'images' => [], 'errors' => [], 'html' => ''];
        $html = '';

        foreach ($request->files as $key => $file) {
            $fileName = Str::slug(pathinfo($request->file($key)->getClientOriginalName(), PATHINFO_FILENAME)) . '.webp';
            if (Storage::disk('public')->has('images' . date("/Y/m/d/") . $fileName)) {
                $fileName = Str::slug(pathinfo($request->file($key)->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.webp';
            }

            $manager = new ImageManager(Driver::class);
            $image = $manager->read($request->file($key));
            $img = $image->encode(new WebpEncoder(quality: 90));

            if (Storage::disk('public')->put('images' . date("/Y/m/d/") . $fileName, (string)$img, 'public')) {
                $path = '/images' . date("/Y/m/d/") . $fileName;
                $media = [
                    'user_id' => Auth::check() ? Auth::user()->id : 0,
                    'path' => $path,
                    'name' => $fileName,
                    'folder' => '',
                    'type' => 0,
                    'favourite' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('media')->insert($media);
                $response['images'][] = $path;
                $results = $this->autoCrop($request->file($key), $ratio, true);
                $response['large_images'][] = (!empty($results['large_images']) ? '/storage'.$results['large_images'] : $path);
                $response['medium_images'][] = (!empty($results['medium_images']) ? '/storage'.$results['medium_images'] : $path);
                $response['thumbnail_images'][] = (!empty($results['thumbnail_images']) ? '/storage'.$results['thumbnail_images'] : $path);

                $html .= '<div class="col-lg-3 col-md-3 col-6" style="position: relative">';
                $html .= '  <image class="img-thumbnail img-responsive" style="width: 150px;" src="' . '/storage'.$results['medium_images'] . '"/>';
                $html .= '  <input class="form-check-input" style="top: 0; left: 15px; position: absolute;" checked type="checkbox" value="' . $path . '" name="album[]">';
                $html .= '</div>';
                $response['status'] = true;
            }
        }
        $response['html'] = (empty($html)) ? '' : '<div class="row">' . $html . '</div>';

        exit(json_encode($response));
    }

    public function autoCrop($file, $ratio)
    {
        $results = [];
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.webp';
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($file);
        if (empty($ratio) || $ratio == 'undefined') {
            $ratio = '16x9';
            $cropSize = $this->{$this->cropSizes[$ratio]};
            $img = (string)$image->scaleDown(width: $cropSize['L']['w'])->toWebp(90);
            if (Storage::disk('public')->put($this->largeImageFolder . $fileName, $img, 'public')) {
                $results['large_images'] = $this->largeImageFolder . $fileName;
            }

            $img = (string)$image->scaleDown(width: $cropSize['M']['w'])->toWebp(90);
            if (Storage::disk('public')->put($this->mediumImageFolder . $fileName, $img, 'public')) {
                $results['medium_images'] = $this->mediumImageFolder . $fileName;
            }

            $img = (string)$image->scaleDown(width: $cropSize['S']['w'])->toWebp(90);
            if (Storage::disk('public')->put($this->thumbnailImageFolder . $fileName, $img, 'public')) {
                $results['thumbnail_images'] = $this->thumbnailImageFolder . $fileName;
            }
        } else {
            $cropSize = $this->{$this->cropSizes[$ratio]};
            $img = (string)$image->contain($cropSize['L']['w'], $cropSize['L']['h'])->toWebp(90);
            if (Storage::disk('public')->put($this->largeImageFolder . $fileName, $img, 'public')) {
                $results['large_images'] = $this->largeImageFolder . $fileName;
            }

            $img = (string)$image->contain($cropSize['M']['w'], $cropSize['M']['h'])->toWebp(90);
            if (Storage::disk('public')->put($this->mediumImageFolder . $fileName, $img, 'public')) {
                $results['medium_images'] = $this->mediumImageFolder . $fileName;
            }

            $img = (string)$image->contain($cropSize['S']['w'], $cropSize['S']['h'])->toWebp(90);
            if (Storage::disk('public')->put($this->thumbnailImageFolder . $fileName, $img, 'public')) {
                $results['thumbnail_images'] = $this->thumbnailImageFolder . $fileName;
            }
        }

        return $results;
    }

    public function crop(Request $request)
    {
        $response = ['status' => true, 'task' => '/upload/crop'];
        $path_parts = pathInfo($request->image);
        $originalImage = Storage::disk('public')->get($request->image);

        if ($path_parts['extension'] == 'webp') {
            $imageSize = [];
            //https://stackoverflow.com/questions/35842807/getimagesize-unable-to-get-size-of-remote-webp-files-with-php-version-5-3-29
            $img = imagecreatefromwebp(storage_path('app/public/' . $request->image));
            $imageSize[0] = imagesx($img);
            $imageSize[1] = imagesy($img);
        } else {
            $imageSize = getimagesize(Storage::disk('public')->url($request->image));
        }

        $dataX = $request->dataX;
        $dataY = $request->dataY;
        $dataHeight = $request->dataHeight;
        $dataWidth = $request->dataWidth;
        $dataRotate = $request->dataRotate;
        $dataScaleX = $request->dataScaleX;
        $dataScaleY = $request->dataScaleY;
        $keepAspectRatio = ($request->keepAspectRatio == 'true') ? true : false;
        $allowUpscaling = ($request->allowUpscaling == 'true') ? true : false;

        if ($dataWidth > 0 && $dataHeight > 0 && ($dataWidth < $imageSize[0] || $dataHeight < $imageSize[1])) {
            $manager = new ImageManager(Driver::class);
            $object = $manager->read($originalImage);

            if ($request->dataResize == 'true') {
                $cropImg = (string)$object->coverDown($dataWidth, $dataHeight)->toWebp(90);
            } else {
                $cropImg = (string)$object->crop($dataWidth, $dataHeight, $dataX, $dataY)->toWebp(90);
            }

            $cropPath = $path_parts['dirname'] . "/crops/" . $path_parts['basename'];
            if (Storage::disk('public')->put($cropPath, $cropImg, 'public')) {
                $response['image'] = $cropPath;
            }
        }

        exit(json_encode($response));
    }

    public function media(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/');
        }

        $user = Auth::user();
        $type = !empty($request->type) ? $request->type : 'image';
        $uploadFolder = '/uploads' . date("/Y/m/d/");
        $allowedExtensions = [
            "image" => [
                "gif",
                "jpeg",
                "png",
                "jpg",
                "svg",
            ],
            "video" => [
                'mp4',
            ],
            "audio" => [
                'mp3',
            ],
            "zip" => [
                'zip',
            ]
        ];

        $allowedMimeTypes = [
            "image" => [
                "image/gif",
                "image/jpeg",
                "image/png",
                "image/bmp",
                "image/svg+xml",
            ],
            "video" => [
                'video/mp4',
            ],
            "audio" => [
                'audio/mp3',
                'audio/mpeg',
                'audio/mpeg3',
                'audio/x-mpeg-3',
                'video/mpeg',
                'video/x-mpeg',
            ],
            "zip" => [
                'application/zip',
                'application/octet-stream',
                'application/x-zip-compressed',
                'multipart/x-zip',
            ]
        ];

        foreach ($request->files as $key => $file) {
            $fileName = Str::slug(pathinfo($request->file($key)->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $request->file($key)->extension();
            if (Storage::disk('public')->has('uploads' . date("/Y/m/d/") . $fileName)) {
                $fileName = Str::slug(pathinfo($request->file($key)->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $request->file($key)->extension();
            }

            foreach ($allowedExtensions as $index => $value) {
                if (in_array($request->file($key)->extension(), $value)) {
                    $type = $index;
                    $response['type'] = $index;
                    break;
                }
            }

            if (!in_array($request->file($key)->extension(), $allowedExtensions[$type]) || !in_array($request->file($key)->getClientMimeType(), $allowedMimeTypes[$type])) {
                Log::info($request->file($key)->extension());
                Log::info($request->file($key)->getClientMimeType());
                $response['errors'][] = __("Tệp tin với định dạng :extension không được chấp nhận!", [$request->file($key)->extension()]);
                exit(json_encode($response));
            }

            if ($fileName = Storage::disk('public')->put($uploadFolder, $request->file($key))) {
                $media = new Media();
                $media->user_id = \auth()->id();
                $media->path = $fileName;
                $media->name = pathinfo($fileName, PATHINFO_BASENAME);
                $media->folder = '';
                $media->type = ($type == 'video' ? 1 : 2);
                $media->save();
                $response['path'] = $fileName;

                switch ($request->file($key)->extension()) {
                    case 'zip':
                        /*                        $zip = new \ZipArchive();
                                                $x = $zip->open($uploadPath . $fileName);
                                                if ($x === true) {
                                                    $extractTo = public_path() . $uploadFolder . $baseName . '/';

                                                    if (!is_dir($extractTo)) {
                                                        mkdir($extractTo, 0777, true);
                                                    }

                                                    $zip->extractTo($extractTo);
                                                    $zip->close();
                                                    $response['extractTo'] = $extractTo;
                                                    $response['path'] = $uploadFolder . $baseName . '/index.htm';
                                                }*/

                        break;

                    case 'gif':
                    case 'jpeg':
                    case 'png':
                    case 'jpg':
                    case 'svg':
                        break;
                }

                $response['status'] = true;
            }
        }

        exit(json_encode($response));
    }
}
