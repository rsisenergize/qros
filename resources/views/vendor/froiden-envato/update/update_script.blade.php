<script type="text/javascript">
    var updateAreaDiv = $('#update-area');
    var refreshPercent = 0;
    var checkInstall = true;

    $('#update-app').click(function () {
        if ($('#update-frame').length) {
            return false;
        }
        @php($envatoUpdateCompanySetting = \Froiden\Envato\Functions\EnvatoUpdate::companySetting())
        // Check if backup module exists and is available
        @php($backupEnabled = module_enabled('Backup'))

        @if(!is_null($envatoUpdateCompanySetting->supported_until) && \Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->isPast())
        let supportText = " Your support has been expired on <b><span id='support-date'>{{\Carbon\Carbon::parse($envatoUpdateCompanySetting->supported_until)->translatedFormat('dS M, Y')}}</span></b>";

        Swal.fire({
            title: "Support Expired",
            html: supportText + "<br>Please renew your support for one-click updates.<br><br> You can still update the application manually by following the documentation <a href='https://froiden.freshdesk.com/support/solutions/articles/43000554421-update-application-manually' target='_blank' class='underline underline-offset-1 ml-2 text-skin-base'>Update Application Manually</a>",
            showCancelButton: true,
            confirmButtonText: "Renew Now",
            denyButtonText: `Free Support Guidelines`,
            cancelButtonText: "Cancel",
            closeOnConfirm: true,
            closeOnCancel: true,
            showCloseButton: true,
            icon: 'warning',
            focusConfirm: false,
            customClass: {
                confirmButton: 'text-white justify-center bg-skin-base hover:bg-skin-base/[.8] sm:w-auto dark:bg-skin-base dark:hover:bg-skin-base/[.8] font-semibold rounded-lg text-sm px-5 py-2.5 text-center',
                denyButton: 'ml-5 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300  shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150  mr-3 p-2',
                cancelButton: 'ml-5 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300  shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(
                    "{{ config('froiden_envato.envato_product_url') }}",
                    '_blank'
                );
            }
        });


        @else



        @endif
        @if($backupEnabled)
            // Backup module exists and is enabled - show backup options
            Swal.fire({
                title: "Update with Backup Options",
                html: `
                    <div class="text-left space-y-4">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="text-sm text-red-800 space-y-2">
                                <div><strong>⚠️ Warning:</strong> Please do not click the <strong>Yes! Update It</strong> button if the application has been customized. Your changes may be lost.</div>
                                <div class="text-xs text-red-700 italic">Please note that the author will not be held responsible for any loss of data or issues that may occur during the update process.</div>
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center mb-3">
                                <span class="text-lg mr-2">🛡️</span>
                                <h3 class="text-lg font-medium text-blue-900">Backup Module Detected</h3>
                            </div>
                            <p class="text-sm text-blue-700 mb-4">Your backup module is enabled. You can create a backup before updating for extra safety.</p>

                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer p-2 rounded hover:bg-blue-100 transition-colors">
                                    <input type="radio" name="backup_option" value="create_backup" class="mr-3" checked>
                                    <span class="text-sm font-medium text-blue-800">Create backup first, then update</span>
                                </label>
                                <label class="flex items-center cursor-pointer p-2 rounded hover:bg-blue-100 transition-colors">
                                    <input type="radio" name="backup_option" value="skip_backup" class="mr-3">
                                    <span class="text-sm font-medium text-blue-800">Skip backup and update directly</span>
                                </label>
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            To confirm if you have read the above message, type <strong><i>confirm</i></strong> in the field.
                        </div>
                    </div>
                `,
                icon: 'info',
                focusConfirm: true,
                customClass: {
                    confirmButton: 'text-white justify-center bg-skin-base hover:bg-skin-base/[.8] sm:w-auto dark:bg-skin-base dark:hover:bg-skin-base/[.8] font-semibold rounded-lg text-sm px-5 py-2.5 text-center',
                    cancelButton: 'ml-5 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300  shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false,
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: "Yes, update it!",
                cancelButtonText: "No, cancel please!",
                showLoaderOnConfirm: true,
                preConfirm: (isConfirm) => {
                    if (!isConfirm) {
                        return false;
                    }

                    if (isConfirm.toLowerCase() !== "confirm") {
                        Swal.fire({
                            title: "Text not matched",
                            html: "You have entered wrong spelling of <b>confirm</b>",
                            icon: 'error',
                        });
                        return false;
                    }

                    // Get selected backup option
                    const backupOption = document.querySelector('input[name="backup_option"]:checked').value;

                    return {
                        confirmed: true,
                        backupOption: backupOption
                    };
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    const backupOption = result.value.backupOption;

                    if (backupOption === 'create_backup') {
                        // Create backup first
                        Swal.fire({
                            title: "Creating Backup...",
                            html: "Please wait while we create a backup of your application before updating.",
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });

                        // Call backup creation
                        $.easyAjax({
                            type: 'POST',
                            url: '{!! route("admin.updateVersion.createBackup") !!}',
                            data: {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: "Backup Created Successfully!",
                                        html: "Your backup has been created. Now proceeding with the update...",
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        proceedWithUpdate();
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Backup Failed",
                                        html: "Failed to create backup: " + response.message + "<br><br>Do you want to continue with the update anyway?",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: "Yes, continue",
                                        cancelButtonText: "Cancel update"
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            proceedWithUpdate();
                                        }
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Backup Failed",
                                    html: "Failed to create backup. Do you want to continue with the update anyway?",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: "Yes, continue",
                                    cancelButtonText: "Cancel update"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        proceedWithUpdate();
                                    }
                                });
                            }
                        });
                    } else {
                        // Skip backup and update directly
                        proceedWithUpdate();
                    }
                }
            });
        @else
            // No backup module or not enabled - show original warning
            Swal.fire({
                title: "Are you sure?",
                html: `
                    <div class="text-left space-y-4">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="text-sm text-red-800 space-y-2">
                                <div><strong>⚠️ Warning:</strong> Please do not click the <strong>Yes! Update It</strong> button if the application has been customized. Your changes may be lost.</div>
                                <div class="text-xs text-red-700 italic">Please note that the author will not be held responsible for any loss of data or issues that may occur during the update process.</div>
                            </div>
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="text-sm text-yellow-800">
                                <strong>💡 Recommendation:</strong> As a precautionary measure, please make a backup of your files and database before updating.
                            </div>
                        </div>

                        <div class="text-sm text-gray-600">
                            To confirm if you have read the above message, type <strong><i>confirm</i></strong> in the field.
                        </div>
                    </div>
                `,
            icon: 'info',
            focusConfirm: true,
            customClass: {
                confirmButton: 'text-white justify-center bg-skin-base hover:bg-skin-base/[.8] sm:w-auto dark:bg-skin-base dark:hover:bg-skin-base/[.8] font-semibold rounded-lg text-sm px-5 py-2.5 text-center',
                cancelButton: 'ml-5 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-sm text-gray-700 dark:text-gray-300  shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No, cancel please!",
            showLoaderOnConfirm: true,
            preConfirm: (isConfirm) => {

                if (!isConfirm) {
                    return false;
                }

                if (isConfirm.toLowerCase() !== "confirm") {

                    Swal.fire({
                        title: "Text not matched",
                        html: "You have entered wrong spelling of <b>confirm</b>",
                        icon: 'error',
                    });
                    return false;
                }
                if (isConfirm.toLowerCase() === "confirm") {
                    return true;
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                updateAreaDiv.removeClass('hidden');
                Swal.close();
                $.easyAjax({
                    type: 'GET',
                    blockUI: true,
                    url: '{!! route("admin.updateVersion.update") !!}',
                    success: function (response) {
                        if (response.status === 'success') {
                            updateAreaDiv.html("<strong>Downloading...:-</strong><br> ");
                            downloadScript();
                            downloadPercent();
                        } else if (response.status === 'fail')
                            updateAreaDiv.addClass('hidden');
                    }
                });
            }
        });
        @endif


    })

    function downloadScript() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.download") !!}',
            success: function (response) {
                clearInterval(refreshPercent);

                if(response.status === 'fail'){
                    updateAreaDiv.html(`<i><span class='text-red-500'><strong>Update Failed</strong> :</span> ${response.message}</i>`)
                    return false;
                }

                $('#percent-complete').css('width', '100%');
                $('#percent-complete').html('100%');
                $('#download-progress').append("<i><span class='text-green-500'>Download complete.</span> Now Installing...Please wait (This may take few minutes.)</i>");

                window.setInterval(function () {
                    /// call your function here
                    if (checkInstall == true) {
                        checkIfFileExtracted();
                    }
                }, 1500);

                installScript();

            }
        });
    }

    function getDownloadPercent() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.downloadPercent") !!}',
            success: function (response) {
                response = response.toFixed(1);
                $('#percent-complete').css('width', response + '%');
                $('#percent-complete').html(response + '%');
            }
        });
    }

    function checkIfFileExtracted() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.checkIfFileExtracted") !!}',
            success: function (response) {
                checkInstall = false;
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    }

    function downloadPercent() {
        updateAreaDiv.append(`<div id="download-progress" class="text-sm font-semibold border-b mb-2 ">
                    <div class="mb-1">Download Progress...</div>
                    <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700 mb-3 ">
                        <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" id="percent-complete" style="width: 0%"> 0%</div>
                    </div>
                </div>`);
        //getting data
        refreshPercent = window.setInterval(function () {
            getDownloadPercent();
            /// call your function here
        }, 1500);
    }

    function installScript() {
        $.easyAjax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.install") !!}',
            success: function (response) {
                if(response.status == 'success'){
                    window.location.reload();
                }
            }
        });
    }

    function getPurchaseData() {
        var token = "{{ csrf_token() }}";
        $.easyAjax({
            type: 'POST',
            url: "{{ route('purchase-verified') }}",
            data: {'_token': token},
            container: "#support-div",
            messagePosition: 'inline',
            success: function (response) {
                window.location.reload();
            }
        });
        return false;
    }

    function proceedWithUpdate() {
        updateAreaDiv.removeClass('hidden');
        Swal.close();
        $.easyAjax({
            type: 'GET',
            blockUI: true,
            url: '{!! route("admin.updateVersion.update") !!}',
            success: function (response) {
                if (response.status === 'success') {
                    updateAreaDiv.html("<strong>Downloading...:-</strong><br> ");
                    downloadScript();
                    downloadPercent();
                } else if (response.status === 'fail')
                    updateAreaDiv.addClass('hidden');
            }
        });
    }
</script>
