@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/css/dropzone.min.css') }}">
    <style>
        .dropzone {
            border: 2px dashed #e2e8f0;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .dropzone:hover {
            border-color: #94a3b8;
        }

        .dropzone .dz-message {
            margin: 2em 0;
        }

        .dropzone .dz-preview .dz-error-message {
            top: 150px !important;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .alert-success {
            color: #065f46;
            background-color: #d1fae5;
            border: 1px solid #059669;
        }

        .alert-danger {
            color: #991b1b;
            background-color: #fee2e2;
            border: 1px solid #dc2626;
        }

        .step-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
            margin-bottom: 1.5rem;
        }

        .step-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .step-content {
            padding: 1.5rem;
        }

        .module-file-item {
            transition: all 0.2s ease;
        }

        .module-file-item:hover {
            transform: translateY(-2px);
        }

        @keyframes highlight-bounce {
            0%, 100% {
                transform: translateY(0);
                box-shadow: 0 0 0 0 rgba(var(--skin-base-rgb), 0.4);
            }
            50% {
                transform: translateY(-3px);
                box-shadow: 0 8px 16px -8px rgba(var(--skin-base-rgb), 0.4);
            }
        }

        @keyframes pulse-glow {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 20px 10px rgba(34, 197, 94, 0.3);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
                transform: scale(1);
            }
        }

        .highlight-install-btn {
            animation: pulse-glow 1.5s ease-in-out infinite;
            background: linear-gradient(45deg, #22c55e, #16a34a) !important;
            position: relative;
            overflow: visible;
        }

        .highlight-install-btn::before {
            content: '👉 @lang('app.clickToInstall')';
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: #1f2937;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0.9;
        }

        .highlight-install-btn::after {
            content: '';
            position: absolute;
            left: 50%;
            top: -8px;
            transform: translateX(-50%);
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #1f2937;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-4 py-2.5">
            <div>
                <a href="{{ route('superadmin.custom-modules.index').'?tab=custom' }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    @lang('menu.goBackToCustomModules')
                </a>
                <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">@lang('modules.update.installCustomModule')</h1>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="px-4 py-6">
        <!-- Step 1 -->
        <div class="step-container dark:bg-gray-800">
            <div class="step-header">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-skin-base text-white font-semibold">1</span>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">@lang('modules.update.uploadModule')</h2>
                </div>
            </div>

            <div class="step-content">
                @php
                    $uploadMaxFilesize = \App\Helper\Files::getUploadMaxFilesize();
                    $postMaxSize = \App\Helper\Files::getPostMaxSize();
                @endphp


                @if(!$uploadMaxFilesize['greater'] || !$postMaxSize['greater'])

                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-md shadow-sm mb-4 dark:bg-amber-900/30 dark:border-amber-600">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-amber-800 dark:text-amber-400 mb-2">Server Configuration Warning</h3>
                                @if(!$uploadMaxFilesize['greater'])
                                    <div class="mb-2">
                                        <p class="text-amber-700 dark:text-amber-300">
                                            <span class="font-semibold">Server upload_max_filesize:</span> {{\App\Helper\Files::getUploadMaxFilesize()['size']}}
                                            <br><span class="font-semibold">Required:</span> <strong>{{\App\Helper\Files::REQUIRED_FILE_UPLOAD_SIZE}}MB</strong>
                                        </p>
                                    </div>
                                @endif
                                @if(!$postMaxSize['greater'])
                                    <div class="mb-2">
                                        <p class="text-amber-700 dark:text-amber-300">
                                            <span class="font-semibold">Server post_max_size:</span> {{\App\Helper\Files::getPostMaxSize()['size']}}
                                            <br><span class="font-semibold">Required:</span> <strong>{{\App\Helper\Files::REQUIRED_FILE_UPLOAD_SIZE}}MB</strong>
                                        </p>
                                    </div>
                                @endif
                                <p class="text-sm text-amber-600 dark:text-amber-400 mt-2">Please update your server configuration to meet the requirements for uploading module files.</p>
                            </div>

                            <div class="ml-4 flex flex-col items-end">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Server Software:</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">{{ $_SERVER['SERVER_SOFTWARE'] }}</span>
                                </div>
                                <div>
                                    @if(str_contains(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache'))
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-green-600 text-white text-xs font-bold rounded-full" title="Apache">Apache Server</span>
                                    @elseif(str_contains(strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx'))
                                        <span class="inline-flex items-center justify-center px-3 py-1 bg-purple-600 text-white text-xs font-bold rounded-full" title="Nginx">Nginx Server</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                @endif

                <div class="mt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">@lang('messages.downloadFilefromCodecanyon')</p>
                    <div id="file-upload-dropzone" class="dropzone dark:bg-gray-800 dark:border-gray-600"></div>
                </div>
            </div>
        </div>

        <div id="install-process"></div>

        <!-- Step 2 -->
        <div class="step-container dark:bg-gray-800 mt-6">
            <div class="step-header">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-skin-base text-white font-semibold">2</span>
                    <div class="max-w-[80%]">
                        <h2 class="text-sm text-gray-600 dark:text-gray-300 mb-4">@lang('modules.update.moduleFile')</h2>
                    </div>
                </div>
            </div>

            <div class="step-content">
                <ul class="space-y-3" id="files-list">
                    @forelse (collect(\Illuminate\Support\Facades\File::files($updateFilePath))->sortByDesc(function($file) {
                        return \Illuminate\Support\Facades\File::lastModified($file);
                    })->filter(function($file) {
                        return \Illuminate\Support\Facades\File::basename($file) !== 'modules_statuses.json';
                    }) as $key => $filename)

                        @if (\Illuminate\Support\Facades\File::basename($filename) != 'modules_statuses.json' && strpos(\Illuminate\Support\Facades\File::basename($filename), 'auto') === false)
                            <li class="module-file-item bg-gray-50 dark:bg-gray-700 rounded-lg" id="file-{{ $key + 1 }}">
                                <div class="flex items-center justify-between p-4">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                            {{ \Illuminate\Support\Facades\File::basename($filename) }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                @lang('app.uploadDate'):
                                                @php
                                                    $lastModified = \Carbon\Carbon::parse(\Illuminate\Support\Facades\File::lastModified($filename))->timezone(global_setting()->timezone);
                                                    $now = \Carbon\Carbon::now(global_setting()->timezone);
                                                    $diffInMinutes = $lastModified->diffInMinutes($now);
                                                @endphp
                                                {{ $lastModified->translatedFormat('jS M, Y g:i A') }}
                                            </p>
                                            @if($diffInMinutes < 10)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    @if($diffInMinutes < 1)
                                                       @lang('app.uploadedNow')
                                                    @else
                                                        @lang('app.uploadedAgo', ['time' => $lastModified->diffForHumans()])
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-skin-base hover:bg-skin-base/90 rounded-lg transition-colors install-files {{ $diffInMinutes < 1 ? 'highlight-install-btn' : '' }}"
                                                data-file-no="{{ $key + 1 }}"
                                                data-file-path="{{ $filename }}">
                                            @lang('modules.update.install')
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                class="inline-flex items-center p-2 text-gray-500 hover:text-red-600 rounded-lg transition-colors delete-files group relative"
                                                data-file-no="{{ $key + 1 }}"
                                                data-file-path="{{ $filename }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                                @lang('app.removeFile')
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endif
                    @empty
                        <li class="text-center text-gray-500 dark:text-gray-400 py-4">
                            @lang('messages.noRecordFound')
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/froiden-helper/helper.js') }}"></script>
<script src="{{ asset('vendor/jquery/dropzone.min.js') }}"></script>

    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function () {
            const uploadFile = "{{ route('superadmin.app-update.store') }}?_token={{ csrf_token() }}";
            const myDrop = new Dropzone("#file-upload-dropzone", {
                url: uploadFile,
                acceptedFiles: 'application/zip, application/x-zip-compressed, application/x-compressed, multipart/x-zip',
                addRemoveLinks: true,
                dictDefaultMessage: "@lang('app.dropFileToUpload')",
            });
            myDrop.on("complete", function (file) {
                if (myDrop.getRejectedFiles().length == 0) {
                    window.location.reload();
                }
            });
        });

        $('.install-files').click(function () {

            $('#install-process').html('<div class="alert alert-primary">@lang("messages.installingUpdateMessage")</div>');

            let filePath = $(this).data('file-path');
            $.easyAjax({
                type: 'POST',
                url: "{{ route('superadmin.custom-modules.store') }}",
                blockUI: true,
                data: {
                    "_token": "{{ csrf_token() }}",
                    filePath: filePath
                },
                success: function (response) {
                    $('#install-process').html('');

                    if (response.status === 'success') {
                        $.easyBlockUI('body')
                        $('#install-process').html(`<div class="alert alert-success">@lang('messages.customModuleInstalled')</div>`);
                        window.location.href = "{{ route('superadmin.custom-modules.index').'?tab=custom' }}";
                    }

                    if (response.status === 'fail') {
                        $('#install-process').html(`<div class="alert alert-danger">${response.message}</div>`);
                    }
                }
            });
        });

        $('.delete-files').click(function () {
            let filePath = $(this).data('file-path');
            let fileNumber = $(this).data('file-no');

            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.removeFileText')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'bg-red-500 text-white hover:bg-red-600 hover:text-white rounded-md p-2 mr-3',
                    cancelButton: 'bg-gray-500 text-white hover:bg-gray-600 hover:text-white rounded-md p-2'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.easyAjax({
                        type: 'POST',
                        url: "{{ route('superadmin.app-update.deleteFile') }}",
                        blockUI: true,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            filePath: filePath
                        },
                        success: function (response) {
                            $('#file-' + fileNumber).remove();
                        }
                    });
                }
            });


        });

    </script>
@endpush
