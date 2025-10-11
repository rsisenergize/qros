<?php

namespace App\Helper;

use Exception;
use App\Models\FileStorage;
use App\Models\StorageSetting;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManagerStatic as Image;

class Files
{

    const UPLOAD_FOLDER = 'user-uploads';
    const IMPORT_FOLDER = 'import-files';

    const REQUIRED_FILE_UPLOAD_SIZE = 20;

    /**
     * @param mixed $image
     * @param string $dir
     * @param null $width
     * @param int $height
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public static function upload($image, string $dir, $width = null, int $height = 800, $name = null)
    {
        // To upload files to local server
        config(['filesystems.default' => 'local']);

        $uploadedFile = $image;
        $folder = $dir . '/';

        try {
            self::validateUploadedFile($uploadedFile);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        $newName = $name ?: self::generateNewFileName($uploadedFile->getClientOriginalName());

        $tempPath = public_path(self::UPLOAD_FOLDER . '/temp/' . $newName);

        /** Check if folder exits or not. If not then create the folder */
        self::createDirectoryIfNotExist($folder);

        $newPath = $folder . '/' . $newName;

        $uploadedFile->storeAs('temp', $newName);

        if (($width && $height) && File::extension($uploadedFile->getClientOriginalName()) !== 'svg') {
            Image::make($tempPath)
                ->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save();
        }

        Storage::put($newPath, File::get($tempPath), ['public']);

        // Deleting temp file
        File::delete($tempPath);


        return $newName;
    }

    /**
     * @throws ApiException
     */
    public static function validateUploadedFile($uploadedFile)
    {
        // Check if file is valid
        if (!$uploadedFile->isValid()) {
            throw new ApiException('File was not uploaded correctly');
        }

        // Disallow dangerous extensions and mime types
        $forbiddenExtensions = [
            'php',
            'php3',
            'php4',
            'php5',
            'phtml',
            'phar',
            'sh',
            'htaccess',
            'pl',
            'cgi',
            'exe',
            'bat',
            'cmd',
            'com',
            'scr',
            'dll',
            'js',
            'jsp',
            'asp',
            'aspx',
            'cer',
            'csr',
            'jsp',
            'jspx',
            'war',
            'jar',
            'vb',
            'vbs',
            'wsf',
            'ps1',
            'ps2',
            'xml'
        ];

        $forbiddenMimeTypes = [
            'text/x-php',
            'application/x-php',
            'application/x-sh',
            'text/x-shellscript',
            'application/x-msdownload',
            'application/x-msdos-program',
            'application/x-executable',
            'application/x-csh',
            'application/x-bat',
            'application/x-msdos-windows',
            'application/x-javascript',
            'text/javascript',
            'application/javascript',
            'application/x-msdownload',
            'application/x-ms-installer',
            'application/x-dosexec',
            'application/x-cgi',
            'application/x-perl',
            'text/x-perl',
            'application/x-python',
            'text/x-python',
            'application/x-msdos-program',
            'application/x-msdos-windows',
            'application/x-msdos-batch',
            'application/x-msdos-cmd',
            'application/x-msdos-com',
            'application/x-msdos-scr',
            'application/x-msdos-dll',
            'application/x-msdos-js',
            'application/x-msdos-vbs',
            'application/x-msdos-ps1',
            'application/xml',
            'text/xml'
        ];

        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        $mimeType = strtolower($uploadedFile->getMimeType());
        $originalName = strtolower($uploadedFile->getClientOriginalName());

        if (preg_match('/\.(php[0-9]?|phtml|phar|sh|pl|cgi|exe|bat|cmd|com|scr|dll|js|jsp|asp|aspx|cer|csr|jspx|war|jar|vb|vbs|wsf|ps1|ps2|xml)(\..+)?$/i', $originalName)) {
            throw new Exception('You are not allowed to upload files with dangerous extensions');
        }

        if (in_array($extension, $forbiddenExtensions)) {
            throw new Exception('You are not allowed to upload files with extension: ' . $extension);
        }

        if (in_array($mimeType, $forbiddenMimeTypes)) {
            throw new Exception('You are not allowed to upload files with mime type: ' . $mimeType);
        }

        if (strpos($originalName, '.htaccess') !== false) {
            throw new Exception('You are not allowed to upload .htaccess files');
        }

        if ($uploadedFile->getSize() <= 10) {
            throw new Exception('You are not allowed to upload a file with filesize less than 10 bytes');
        }

        if (empty($extension)) {
            throw new Exception('File must have a valid extension');
        }

        if (strlen($uploadedFile->getClientOriginalName()) > 255) {
            throw new Exception('File name is too long');
        }
    }

    public static function generateNewFileName($currentFileName)
    {
        $ext = strtolower(File::extension($currentFileName));
        $newName = md5(microtime());

        return ($ext === '') ? $newName : $newName . '.' . $ext;
    }

    /**
     * @throws \Exception
     */
    public static function uploadLocalOrS3($uploadedFile, $dir, $width = null, int $height = 800, $name = null)
    {
        try {
            self::validateUploadedFile($uploadedFile);
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        // Create directory and store temp file
        self::createDirectoryIfNotExist($dir);

        try {
            $newName = $name ?: self::generateNewFileName($uploadedFile->getClientOriginalName());

            // Process image if dimensions provided
            if ($width && $height) {
                self::processImage($uploadedFile, $dir, $newName, $width, $height);
            } else {
                // Store file directly
                $uploadedFile->storeAs($dir, $newName, config('filesystems.default'));
            }

            // Store file metadata
            self::fileStore($uploadedFile, $dir, $newName);

            // Verify upload for Livewire files
            if ($uploadedFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                Storage::disk(config('filesystems.default'))->exists($dir . '/' . $newName);
            }

            return $newName;
        } catch (\Exception $e) {
            throw new \Exception(__('app.fileNotUploaded') . ' ' . $e->getMessage() . ' on ' . config('filesystems.default'));
        }
    }

    /**
     * Process and store an image file with optional resizing
     *
     * @param UploadedFile $uploadedFile The uploaded image file
     * @param string $dir Target directory path
     * @param string $newName Generated filename
     * @param int|null $width Target width for resizing
     * @param int $height Target height for resizing
     */
    private static function processImage($uploadedFile, $dir, $newName, $width = null, $height = 800)
    {
        $tempPath = public_path(self::UPLOAD_FOLDER . '/temp/' . $newName);
        $newPath = $dir . '/' . $newName;

        // store temp file
        $uploadedFile->storeAs('temp', $newName, 'local');

        // Check if image can be resized
        $fileExt = File::extension($uploadedFile->getClientOriginalName());
        if ($width && $height && !in_array($fileExt, ['svg', 'webp', 'ico'])) {


            Image::make($tempPath)
                ->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save();
        }

        // Store processed file
        Storage::disk(config('filesystems.default'))->put($newPath, File::get($tempPath));

        // Cleanup
        File::delete($tempPath);
    }

    public static function fileStore($file, $folder, $generateNewName = '', $uploaded = false, $restaurantId = null)
    {
        // Generate a new name if $generateNewName is empty
        $newName = $generateNewName ?: self::generateNewFileName($file->getClientOriginalName());

        // Retrieve enabled storage setting
        $setting = StorageSetting::where('status', 'enabled')->first();
        $storageLocation = $setting ? $setting->filesystem : 'local';

        // Store file information in the database
        $fileStorage = new FileStorage();
        $fileStorage->restaurant_id = $restaurantId;
        $fileStorage->filename = $newName;
        $fileStorage->size = $file->getSize();
        $fileStorage->type = $uploaded ? $file->getClientMimeType() : $file->getMimeType();
        $fileStorage->path = $folder;
        $fileStorage->storage_location = $storageLocation;
        $fileStorage->save();

        return $newName;
    }

    /**
     * Delete a file from storage and its database record
     *
     * @param string $filename Name of file to delete
     * @param string $folder Folder path where file is stored
     * @return bool Always returns true to prevent errors from bubbling up
     */
    public static function deleteFile($filename, $folder)
    {
        $dir = trim($folder, '/');
        $filePath = $dir . '/' . $filename;

        // Delete database record if exists
        FileStorage::where('filename', $filename)->first()?->delete();

        // Get configured storage disk
        $disk = Storage::disk(config('filesystems.default'));

        // Handle cloud storage (S3, etc)
        if (in_array(config('filesystems.default'), StorageSetting::S3_COMPATIBLE_STORAGE)) {
            try {
                $disk->exists($filePath) && $disk->delete($filePath);
            } catch (\Exception) {
            }
        }
        // Handle local storage
        else {
            $path = public_path(Files::UPLOAD_FOLDER . '/' . $filePath);
            try {
                File::exists($path) && File::delete($path);
            } catch (\Throwable) {
                return true;
            }
        }

        return true;
    }

    public static function deleteDirectory($folder)
    {
        $dir = trim($folder);
        try {
            Storage::deleteDirectory($dir);
        } catch (\Exception $e) {
            return true;
        }


        return true;
    }

    public static function copy($from, $to)
    {
        Storage::disk(config('filesystems.default'))->copy($from, $to);
    }

    public static function createDirectoryIfNotExist($folder)
    {
        $directoryPath = public_path(self::UPLOAD_FOLDER . '/' . $folder);

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0775, true);
        }
    }

    public static function uploadLocalFile($fileName, $path, $companyId = null): void
    {
        if (!File::exists(public_path(Files::UPLOAD_FOLDER . '/' . $path . '/' . $fileName))) {
            return;
        }

        self::saveFileInfo($fileName, $path, $companyId);
        self::storeLocalFileOnCloud($fileName, $path);
    }

    public static function saveFileInfo($fileName, $path, $companyId = null)
    {
        $filePath = public_path(Files::UPLOAD_FOLDER . '/' . $path . '/' . $fileName);

        $fileStorage = FileStorage::where('filename', $fileName)->first() ?: new FileStorage();
        $fileStorage->company_id = $companyId;
        $fileStorage->filename = $fileName;
        $fileStorage->size = File::size($filePath);
        $fileStorage->type = File::mimeType($filePath);
        $fileStorage->path = $path;
        $fileStorage->storage_location = config('filesystems.default');
        $fileStorage->save();
    }

    public static function storeLocalFileOnCloud($fileName, $path)
    {
        if (config('filesystems.default') != 'local') {
            $filePath = public_path(Files::UPLOAD_FOLDER . '/' . $path . '/' . $fileName);
            try {
                $contents = File::get($filePath);
                Storage::disk(config('filesystems.default'))->put($path . '/' . $fileName, $contents);
                // TODO: Delete local file in Next release
                // File::delete($filePath);
                return true;
            } catch (\Exception $e) {
                info($e->getMessage());
            }
        }

        return false;
    }

    public static function getFormattedSizeAndStatus($maxSizeKey)
    {
        try {
            // Retrieve the raw value from php.ini
            $maxSize = ini_get($maxSizeKey);

            // Convert the size to bytes
            $sizeInBytes = self::returnBytes($maxSize);

            // Format the size in either MB or GB
            if ($sizeInBytes >= 1 << 30) {
                return [
                    'size' => round($sizeInBytes / (1 << 30), 2) . ' GB',
                    'greater' => true
                ];
            }

            $mb = $sizeInBytes / 1048576;

            if ($sizeInBytes >= 1 << 20) {
                return [
                    'size' => round($sizeInBytes / (1 << 20), 2) . ' MB',
                    'greater' => $mb >= self::REQUIRED_FILE_UPLOAD_SIZE
                ];
            }

            if ($sizeInBytes >= 1 << 10) {
                return [
                    'size' => round($sizeInBytes / (1 << 10), 2) . ' KB',
                    'greater' => false
                ];
            }

            return [
                'size' => $sizeInBytes . ' Bytes',
                'greater' => false
            ];
        } catch (\Exception $e) {
            return [
                'size' => '0 Bytes',
                'greater' => true
            ];
        }
    }

    public static function getUploadMaxFilesize()
    {
        return self::getFormattedSizeAndStatus('upload_max_filesize');
    }

    public static function getPostMaxSize()
    {
        return self::getFormattedSizeAndStatus('post_max_size');
    }

    // Helper function to convert human-readable size to bytes
    public static function returnBytes($val)
    {
        $val = trim($val);
        $valNew = substr($val, 0, -1);
        $last = strtolower($val[strlen($val) - 1]);

        switch ($last) {
            case 'g':
                $valNew *= 1024;
            case 'm':
                $valNew *= 1024;
            case 'k':
                $valNew *= 1024;
        }

        return $valNew;
    }

    public static function moveFilesLocalToAwsS3()
    {
        $files = FileStorage::where('storage_location', 'local')->get();

        foreach ($files as $file) {
            $filePath = public_path(Files::UPLOAD_FOLDER . '/' . $file->path . '/' . $file->filename);

            if (!File::exists($filePath)) {
                $file->delete();
                continue;
            }

            $contents = File::get($filePath);
            $uploaded = Storage::disk(config('filesystems.default'))->put($file->path . '/' . $file->filename, $contents);

            if ($uploaded) {
                $file->storage_location = config('filesystems.default') === 's3' ? 'aws_s3' : config('filesystems.default');
                $file->save();
                Files::deleteFileFromLocal($filePath);
            }
        }

        return true;
    }

    public static function deleteFileFromLocal($filePath)
    {
        File::delete($filePath);
    }
}
