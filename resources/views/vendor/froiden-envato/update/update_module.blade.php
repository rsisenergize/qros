<script type="text/javascript">
    $(document).ready(function() {

        let updateAreaDiv = $('#update-area');
        let refreshPercent = 0;
        let checkInstall = true;
        let moduleName = '';
        let productURL = '';

        $('.change-module-notification').click(function() {

            moduleName = $(this).data('module-name');
            let notifyhUrl = '{!! route('admin.updateVersion.notify', [':moduleName']) !!}';
            notifyhUrl = notifyhUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'POST',
                blockUI: true,
                url: notifyhUrl,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'status': $(this).is(':checked') ? 1 : 0
                },
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.reload();
                    }
                }
            });
        });

        $('.refreshModule').click(function() {
            moduleName = $(this).data('module-name');
            let refreshUrl = '{!! route('admin.updateVersion.refresh', [':moduleName']) !!}';
            refreshUrl = refreshUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'GET',
                blockUI: true,
                url: refreshUrl
            });
        });

        $('.update-module').click(function() {
            if ($('#update-frame').length) {
                return false;
            }

            moduleName = $(this).data('module-name');
            productURL = $(this).data('product-url');

            if (moduleName == '') {
                return false;
            }

            // check if module is supported
            let supportCheckUrl = '{!! route('admin.updateVersion.checkSupport', [':moduleName']) !!}';
            supportCheckUrl = supportCheckUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'GET',
                blockUI: true,
                url: supportCheckUrl,
                success: function(response) {
                    if (response.status === 'fail') {
                        Swal.fire({
                            title: "Support Expired",
                            html: response.message,
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
                                confirmButton: 'btn btn-primary mr-3',
                                denyButton: 'btn btn-success mr-3 p-2',
                                cancelButton: 'btn btn-secondary'
                            },
                            showClass: {
                                popup: 'swal2-noanimation',
                                backdrop: 'swal2-noanimation'
                            },
                            buttonsStyling: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(
                                    productURL,
                                    '_blank'
                                );
                            }
                        });
                    } else if (response.status === 'success') {
                        Swal.fire({
                            title: "Are you sure?",
                            html: `<x-alert type="danger" icon="info-circle">

                            <div>Please do not click the <strong>Yes! Update It</strong> button if the application has been customized. Your changes may be lost.\n
                                <br>
                                <br>
                                <div>As a precautionary measure, please make a backup of your files and database before updating.. \</div>
                                <br>
                                <strong class="dark:text-white"><i>Please note that the author will not be held responsible for any loss of data or issues that may occur during the update process.</i></strong>
                            </div>
                                </x-alert>
                                <span class="text-base text-gray-900 dark:text-white">To confirm if you have read the above message, type <strong><i>confirm</i></strong> in the field.</span>
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
                                updateAreaDiv.removeClass('d-none');
                                Swal.close();
                                let updateUrl = '{!! route('admin.updateVersion.update', [':moduleName']) !!}';
                                updateUrl = updateUrl.replace(':moduleName', moduleName);
                                $.easyBlockUI('body');

                                $.easyAjax({
                                    type: 'GET',
                                    url: updateUrl,
                                    success: function(response) {
                                        if (response.status === 'success') {
                                            updateAreaDiv.html("<strong>Downloading...:-</strong><br> ");
                                            downloadScript();
                                            downloadPercent();
                                        } else if (response.status === 'fail') {
                                            $.easyUnblockUI('body');
                                            updateAreaDiv.html(`<i><span class='text-red'><strong>Update Failed</strong> :</span> ${response.message}</i>`)
                                            // updateAreaDiv.addClass('d-none');
                                        }
                                    }
                                });
                            }
                        });
                    }
                }
            });

        })

        function downloadScript() {
            let downloadUrl = '{!! route('admin.updateVersion.download', [':moduleName']) !!}';
            downloadUrl = downloadUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'GET',
                url: downloadUrl,
                success: function(response) {
                    clearInterval(refreshPercent);

                    if(response.status === 'fail'){
                        $.easyUnblockUI('body');
                        updateAreaDiv.html(`<i><span class='text-red'><strong>Update Failed</strong> :</span> ${response.message}</i>`)
                        return false;
                    }

                    $('#percent-complete').css('width', '100%');
                    $('#percent-complete').html('100%');
                    $('#download-progress').append(
                        "<i><span class='text-success'>Download complete.</span> Now Installing...Please wait (This may take few minutes.)</i>"
                    );

                    window.setInterval(function() {
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
            let downloadPercentUrl = '{!! route('admin.updateVersion.downloadPercent', [':moduleName']) !!}';
            downloadPercentUrl = downloadPercentUrl.replace(':moduleName', moduleName);
            $.easyAjax({
                type: 'GET',
                url: downloadPercentUrl,
                success: function(response) {
                    response = response.toFixed(1);
                    $('#percent-complete').css('width', response + '%');
                    $('#percent-complete').html(response + '%');
                }
            });
        }

        function checkIfFileExtracted() {
            let checkUrl = '{!! route('admin.updateVersion.checkIfFileExtracted', [':moduleName']) !!}';
            checkUrl = checkUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'GET',
                url: checkUrl,
                success: function(response) {
                    checkInstall = false;
                    if (response.status == 'success') {
                        window.location.reload();
                    }
                }
            });
        }

        function downloadPercent() {
            updateAreaDiv.append('<hr><div id="download-progress">' +
                'Download Progress<br><div class="progress progress-lg">' +
                '<div class="progress-bar progress-bar-success active progress-bar-striped" id="percent-complete" role="progressbar""></div>' +
                '</div>' +
                '</div>'
            );
            //getting data
            refreshPercent = window.setInterval(function() {
                getDownloadPercent();
                /// call your function here
            }, 1500);
        }

        function installScript() {
            let installUrl = '{!! route('admin.updateVersion.install', [':moduleName']) !!}';
            installUrl = installUrl.replace(':moduleName', moduleName);

            $.easyAjax({
                type: 'GET',
                url: installUrl,
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.reload();
                    } else if(response.status === 'fail'){
                        checkInstall = false;
                        $.easyUnblockUI('body');
                        updateAreaDiv.html(`<i><span class='text-red'><strong>Update Failed</strong> :</span> ${response.message}</i>`)
                    }
                }
            });
        }

        // $("body").tooltip({
        //     selector: '[data-toggle="tooltip"]'
        // })
    });
</script>
