<?php

namespace App\Controllers;

use Nevs\Controller;
use Nevs\Response;
use Nevs\Config;
use App\Models\Upload;

class UploadController extends Controller
{
    public function Upload(): Response
    {
        $response = [];

        $file = $this->request->files['file'];

        $base_file_name = $file['name'];

        $file_name = $base_file_name;
        $increment = 0;

        $base_path = Config::Get('app_root') . 'Storage/Uploads/';

        while (file_exists($base_path . $file_name)) {
            $increment++;
            $file_name = $increment . $base_file_name;
        }

        if (move_uploaded_file($file['tmp_name'], $base_path . $file_name)) {
            $hash = '';
            $existing_uploads = [''];
            while (count($existing_uploads) > 0) {
                $hash = md5(rand());
                $existing_uploads = Upload::Select('hash=?', [$hash]);
            }

            $upload = Upload::Create([
                'real_name' => $file_name,
                'original_name' => $base_file_name,
                'hash' => $hash,
                'public' => true
            ]);

            $response['success'] = true;
            $response['file'] = [
                'name' => $base_file_name,
                'id' => $upload->id,
                'link' => Config::Get('app_url') . 'upload/' . $upload->hash
            ];
        } else {
            $response['success'] = false;
        }

        return new Response(json_encode($response));
    }

    public function Get(): Response
    {
        if (!isset($this->request->parameters['hash'])) return new Response(json_encode(['error' => 'file hash not set']), ['HTTP/1.1 400 Bad Request']);
        $file_path = null;
        $original_name = '';
        foreach (Upload::Select('hash=?', [$this->request->parameters['hash']]) as $file) {
            $file_path = Config::Get('app_root') . 'Storage/Uploads/' . $file->real_name;
            $original_name = $file->original_name;
        }
        if ($file_path == null) return new Response(json_encode(['error' => 'file not found']), ['HTTP/1.1 400 Bad Request']);

        if (file_exists($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$original_name.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
        }

        return new Response("");
    }
}