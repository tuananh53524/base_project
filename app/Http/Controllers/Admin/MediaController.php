<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = DB::table('media');
        $filter = ['type' => 0, 'from' => '', 'to' => ''];
        $filter['folder'] = $request->query('folder', '');
        $filter['type'] = $request->query('type', 9);

        if ($request->has('type') && $filter['type'] != 9) {
            $query->where('type', '=', $request->type);
        }

        if ($request->has('view') && $request->view == 'tree') {
            $query->where('folder', 'like', $filter['folder']);
        }

        if ($request->has('from')) {
            $filter['from'] = $request->from;
            $query->where('created_at', '>=', $request->from);
        }

        if ($request->has('to')) {
            $filter['to'] = $request->to;
            $query->where('created_at', '<=', $request->to);
        }

        $query->orderBy('created_at', 'desc');
        $records = $query->paginate(36);

        foreach ($records as $key => $value) {
            $thumbnail = str_replace('images/', 'images/thumbnail/', $value->path);
            $records[$key]->thumb = Storage::disk('public')->has($thumbnail) ? $thumbnail : $value->path;
            $records[$key]->thumb = Storage::disk('public')->url($value->path);
        }

        return view('dashboard.media.index', [
            "records" => $records,
            'filter' => $filter,
            'view' => (empty($request->view) ? '' : $request->view)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (empty($request->file('files'))) {
            return redirect('dashboard/media/index');
        }

        $allowedMimeTypes = [
            "image" => [
                "image/gif",
                "image/jpeg",
                "image/png",
                "image/bmp",
            ],
            "video" => [
                'video/mp4',
            ],
            "audio" => [
                'audio/mp3',
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

        /*$config = config('app.dimensions');
        $defaultCropSizes = 'L:' . $config['IMAGE_LARGE_WIDTH'] . 'x' . $config['IMAGE_LARGE_HEIGHT'] . '|M:' . $config['IMAGE_MEDIUM_WIDTH'] . 'x' . $config['IMAGE_MEDIUM_HEIGHT'] . '|S:' . $config['IMAGE_THUMBNAIL_WIDTH'] . 'x' . $config['IMAGE_THUMBNAIL_HEIGHT'];
        $uploadInstance = new UploadController();
        $cropSize = $uploadInstance->setCropSizes($defaultCropSizes, $uploadInstance->ratioRate[4]);*/

        $extraPath = '/' . (empty($type) ? 'images' : 'uploads') . date("/Y/m/d/");
        $folder = '';
        if (!empty($request->folder)) {
            $folder = str_replace('  ', ' ', $request->folder);
            $folder = str_replace(' ', '-', $folder);
            $extraPath .= $folder . '/';
        }

        foreach ($request->file('files') as $key => $file) {
            $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->extension();
            if (Storage::disk('public')->has('images' . $extraPath . $fileName)) {
                $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $file->extension();
            }

            $image = Image::make($file)->encode($file->extension());
            if (Storage::disk('public')->put('images' . date("/Y/m/d/") . $fileName, (string)$image, 'public')) {
                foreach ($allowedMimeTypes as $index => $mimeTypes) {
                    foreach ($mimeTypes as $mimeType) {
                        if ($file->getMimeType() == $mimeType) {
                            $type = $index;
                            break;
                        }
                    }
                }

                $media = [
                    'user_id' => Auth::check() ? Auth::user()->id : 0,
                    'path' => $extraPath . $fileName,
                    'name' => $fileName,
                    'folder' => $folder,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'log' => ''
                ];

                if (!empty($type)) {
                    $mediaTypes = ['image' => 0, 'video' => 1, 'audio' => 2];
                    $media['type'] = $mediaTypes[$type];
                }

                DB::table('media')->insert($media);
                $response['images'][] = 'images' . date("/Y/m/d/") . $fileName;

                if (!empty($cropSize)) {
                    //$results = $uploadInstance->store($file);
                }
            }
        }

        return redirect()->route('media.index');
    }

    public function crop()
    {
        return view('dashboard.media.crop');
    }
}
