(function($) {
    var _correction_ind = 0;
    var app = {
        bindDeleteBtns: function() {
            $('.delete-correction:not(.loaded)').each(function() {
                $(this).addClass('loaded');
                $(this).click(function() {
                    $(this).parents('tr').remove();
                });
            });
        },
        init: function() {
            $('#ncar-main').DataTable({
                order: [
                    [0, 'desc']
                ],
            });
            $('#btn-add').click(function() {
                $('#add-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            });
            $('[data-toggle="tooltip"]').tooltip();
            $('form').on('submit', function(e) {
                e.preventDefault();
            });
            $('#main_form_save').on('click', function(e) {
                e.preventDefault();
                /*files*/
                _files = [];
                $('#ncar_main_form .evidences input').each(function() {
                    _files.push($(this).val());
                });
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_save',
                        data: $('#ncar_main_form').serializeArray(),
                        evidences: _files
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            $('#edit_form_save').on('click', function(e) {
                e.preventDefault();
                /*files*/
                _files = [];
                $('#ncar_edit_form .evidences input').each(function() {
                    _files.push($(this).val());
                });
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_edit_save',
                        data: $('#ncar_edit_form').serializeArray(),
                        evidences: _files,
                        ncar_no: ncar_no
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            $('#add_correction').click(function() {
                date = new Date;
                today = new Date;
                var defaultDate = today.toISOString().split('T')[0];
                $html = '' + '<tr>' + '<td colspan="5"><textarea class="form-control correction_text" rows="5"></textarea></td>' + '<td><input type="date" readonly class="form-control correction_date test" value="' + defaultDate + '"></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
                _correction_ind++;
                $('#form_2_1').append($html);
                app.bindDeleteBtns();
            });
            $('#add_correction_rca').click(function() {
                $html = '' + '<tr class="rca">' + '<td colspan="5"><textarea class="form-control correction_text" rows="5"></textarea></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
                 _correction_ind++;
                $('#form_2_2').append($html);
                app.bindDeleteBtns();
            });
            /*corrective action*/
            $('#add_corrective_action').click(function() {
                date = new Date;
                today = new Date;
                var defaultDate = today.toISOString().split('T')[0];
                $html = '' + '<tr>' + '<td colspan="4"><textarea class="form-control root_causes" rows="5"></textarea></td>' + '<td><textarea class="form-control corrective_action" rows="5"></textarea></td>' + '<td><input type="date" class="form-control corrective_date" value="' + defaultDate + '"></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
               _correction_ind++;
                $('#form_2_3').append($html);
                app.bindDeleteBtns();
            });
            $('#edit_form2_save').click(function(e) {
                e.preventDefault();
                correction = [];

                ncar_no = $('#edit-modal [name="ncar_no"]').val();

                let isValid = true;

                $('#form_2_1 tr').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_date = $(this).find('.correction_date').val();
                    correction_implemented = ($(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '');
                    correction_remarks = $(this).find('.correction_remarks').val();

                    if (!correction_text || !correction_date ) {
                        alert('All fields in Correction must be filled out!');
                        isValid = false; // Mark as invalid
                        return false; // Break the loop
                    }

                    correction.push({
                        correction_text: correction_text,
                        correction_date: correction_date,
                        correction_implemented: correction_implemented,
                        correction_remarks: correction_remarks
                    });
                });

                if (!isValid) return false;

                correction_rca = [];
                $('#form_2_2 tr.rca').each(function() {
                    correction_text = $(this).find('.correction_text').val();

                    if (!correction_text) {
                        alert('All fields in Root Cause Analysis must be filled out!');
                        isValid = false; // Mark as invalid
                        return false; // Break the loop
                    }

                    correction_rca.push({
                        correction_text: correction_text,
                    });
                });

                if (!isValid) return false;

                corrective_action_data = [];
                $('#form_2_3 tr').each(function() {
                    root_causes = $(this).find('.root_causes').val();
                    corrective_action = $(this).find('.corrective_action').val();
                    corrective_date = $(this).find('.corrective_date').val();
                    corrective_implemented = ($(this).find('.corrective_implemented:checked') ? $(this).find('.corrective_implemented:checked').val() : '');
                    corrective_remarks = $(this).find('.corrective_remarks').val();

                    if (!root_causes || !corrective_action || !corrective_date ) {
                        alert('All fields in Corrective Action Plan must be filled out!');
                        isValid = false; // Mark as invalid
                        return false; // Break the loop
                    }

                    corrective_action_data.push({
                        root_causes: root_causes,
                        corrective_action: corrective_action,
                        corrective_date: corrective_date,
                        corrective_implemented: corrective_implemented,
                        corrective_remarks: corrective_remarks
                    });
                });

                if (!isValid) return false;

                files = [];
                $('#part2 #form_2_2 .file-group input').each(function() {
                    files.push($(this).val());
                });

                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_form2_save',
                        data: {
                            correction: correction,
                            correction_rca: correction_rca,
                            files: files,
                            corrective_action_data: corrective_action_data,
                            ncar_no: ncar_no
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            $('#edit_form2_save_satisfactory').click(function(e) {
                e.preventDefault();
                correction = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_2_1_b tr').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_date = $(this).find('.correction_date').val();
                    correction_implemented = ($(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '');
                    correction_remarks = $(this).find('.correction_remarks').val();
                    correction.push({
                        correction_text: correction_text,
                        correction_date: correction_date,
                        correction_implemented: correction_implemented,
                        correction_remarks: correction_remarks,
                    });
                });

                correction_rca = [];
                $('#form_2_2_b tr.rca').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_rca.push({
                        correction_text: correction_text,
                    });
                });

                corrective_action_data = [];
                $('#form_2_3_b tr').each(function() {
                    root_causes = $(this).find('.root_causes').val();
                    corrective_action = $(this).find('.corrective_action').val();
                    corrective_date = $(this).find('.corrective_date').val();
                    corrective_implemented = ($(this).find('.corrective_implemented:checked') ? $(this).find('.corrective_implemented:checked').val() : '');
                    corrective_remarks = $(this).find('.corrective_remarks').val();
                    corrective_action_data.push({
                        root_causes: root_causes,
                        corrective_action: corrective_action,
                        corrective_date: corrective_date,
                        corrective_implemented: corrective_implemented,
                        corrective_remarks: corrective_remarks,
                    });
                });
                files = [];
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_form2_save',
                        data: {
                            correction: correction,
                            correction_rca: correction_rca,
                            files: files,
                            corrective_action_data: corrective_action_data,
                            ncar_no: ncar_no,
                            satisfactory: 1
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            $('#edit_form2_save_not_satisfactory').click(function(e) {
                e.preventDefault();
                correction = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_2_1_b tr').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_date = $(this).find('.correction_date').val();
                    correction_implemented = ($(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '');
                    correction_remarks = $(this).find('.correction_remarks').val();
                    correction.push({
                        correction_text: correction_text,
                        correction_date: correction_date,
                        correction_implemented: correction_implemented,
                        correction_remarks: correction_remarks,
                    });
                });
                correction_rca = [];
                $('#form_2_2_b tr.rca').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_rca.push({
                        correction_text: correction_text,
                    });
                });

                corrective_action_data = [];
                $('#form_2_3_b tr').each(function() {
                    root_causes = $(this).find('.root_causes').val();
                    corrective_action = $(this).find('.corrective_action').val();
                    corrective_date = $(this).find('.corrective_date').val();
                    corrective_implemented = ($(this).find('.corrective_implemented:checked') ? $(this).find('.corrective_implemented:checked').val() : '');
                    corrective_remarks = $(this).find('.corrective_remarks').val();
                    corrective_action_data.push({
                        root_causes: root_causes,
                        corrective_action: corrective_action,
                        corrective_date: corrective_date,
                        corrective_implemented: corrective_implemented,
                        corrective_remarks: corrective_remarks,
                    });
                });
                files = [];
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_form2_save',
                        data: {
                            correction: correction,
                            correction_rca: correction_rca,
                            files: files,
                            corrective_action_data: corrective_action_data,
                            ncar_no: ncar_no,
                            satisfactory: 2
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            /*form 3*/
            $('#add_verification').click(function() {
                date = new Date;
                $html = '' + '<tr>' + '<td colspan="3"><textarea class="form-control input-sm verification_remarks" placeholder="remarks" rows="3"></textarea>' + '<input type="radio" name="verification_' + _correction_ind + '" class="verification_implemented hidden" value="Yes"> ' + '<input type="radio" name="verification_' + _correction_ind + '" class="hidden verification_implemented" value="No">' + '</td>'  + '<td><input type="date" class="form-control input-sm verification_date" value="' + date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate() + '"></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
                _correction_ind++;
                $('#form_3_1').append($html);
                app.bindDeleteBtns();
            });
            $('#edit_form3_save_satisfactory').click(function(e) {
                e.preventDefault();
                verification = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1 tr').each(function() {
                    verification_date = $(this).find('.verification_date').val();
                    verification_implemented = ($(this).find('.verification_implemented:checked') ? $(this).find('.verification_implemented:checked').val() : '');
                    verification_remarks = $(this).find('.verification_remarks').val();
                    verification.push({
                        verification_date: verification_date,
                        verification_implemented: verification_implemented,
                        verification_remarks: verification_remarks,
                    });
                });
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_form3_save',
                        data: {
                            verification: verification,
                            ncar_no: ncar_no,
                            final_decision: 'satisfactory'
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            $('#edit_form3_save_not_satisfactory').click(function(e) {
                e.preventDefault();
                verification = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1 tr').each(function() {
                    verification_date = $(this).find('.verification_date').val();
                    verification_implemented = ($(this).find('.verification_implemented:checked') ? $(this).find('.verification_implemented:checked').val() : '');
                    verification_remarks = $(this).find('.verification_remarks').val();
                    verification.push({
                        verification_date: verification_date,
                        verification_implemented: verification_implemented,
                        verification_remarks: verification_remarks,
                    });
                });
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_form3_save',
                        data: {
                            verification: verification,
                            ncar_no: ncar_no,
                            final_decision: 'not_satisfactory'
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            /*ncar remarks*/
            $('#save_remarks').click(function(e) {
                e.preventDefault();
                verification = [];
                ncar_no = $('#remarks-modal [name="ncar_no"]').val();
                remarks = $('#remarks-modal #ncar_remarks').val();
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_save_remarks',
                        data: {
                            remarks: remarks,
                            ncar_no: ncar_no
                        },
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        Swal.close();
                        if (r.post_id) {
                            $icon = 'success';
                            $title = 'NCAR Saved';
                            $text = '';
                        } else {
                            $icon = 'error';
                            $title = 'NCAR Not Saved';
                            $text = 'Error occurred';
                        }
                        Swal.fire({
                            icon: $icon,
                            title: $title,
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: $text,
                        }).then(function(result) {
                            location.reload();
                        });
                    },
                    beforeSend: function() {
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Saving. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                    }
                });
            });
            /*uploader*/
            $('.file-upload .upload-btn').on('click', function() {
                $this = $(this).parents('.file-upload');
                multiple = ($this.data('multiple-upload') ? true : false);
                if (typeof _uploader != 'undefined') {
                    _uploader.open();
                    return;
                }
                _uploader = wp.media({
                    title: 'Upload Files',
                    multiple: multiple
                }).on('select', function(e) {
                    $('body').addClass('modal-open');
                    /** This will return the selected image from the featured-image Uploader, the result is an object */
                    uploaded_files = _uploader.state().get('selection');
                    uploaded_files = uploaded_files.toJSON();
                    list = '';
                    uploaded_files.forEach(function(v, i) {
                        list += '<input type="hidden" data-url="' + v.url + '" value="' + v.id + '" data-title="' + v.title + '" class="evidences">';
                    });
                    $this.find('.file-group').html(list);
                    if (list) {
                        $this.find('.selected_files').val(uploaded_files.length + ' file(s) selected');
                    }
                });
                _uploader.on('open', function() {
                    /*reassign selected files*/
                    // if ( typeof uploaded_files != 'undefined' ) {
                    // 	var lib = _uploader.state().get('library');
                    // 	uploaded_files.forEach(function(v, i){
                    // 		attachment = wp.media.attachment(v.id);
                    //         attachment.fetch();
                    //         console.log( attachment );
                    //         lib.add(attachment);
                    // 	});
                    // }
                });
                _uploader.on('close', function() {
                    $('body').addClass('modal-open');
                });
                _uploader.open();
            });
            /*view evidences*/
            $('.selected_files').click(function() {
                if ($('.evidences input').length) {
                    $this = $(this).parents('.file-upload');
                    html = '';
                    $this.find('.evidences input').each(function() {
                        let url = $(this).data('url');

                        // Force the URL to start with 'https://'
                        url = url.replace(/^http:\/\//i, 'https://');

						html += '<a href="'+url+'" target="_blank">'+$(this).data('title')+'</a>';
                        Swal.fire({
                            icon: 'info',
                            title: 'Selected File(s)',
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: html,
                        });
                    });
                }
            });
            $('.noncoformity-evidence-file-view').click(function() {
                if ($('#noncoformity-evidence input').length) {
                    $this = $('.noncoformity-evidence-file-upload');
                    html = '';
                    $this.find('#noncoformity-evidence input').each(function() {
                        let url = $(this).data('url');

                        // Force the URL to start with 'https://'
                        url = url.replace(/^http:\/\//i, 'https://');  // Replace http:// with https://

                        html += '<a href="' + url + '" target="_blank">' + $(this).data('title') + '</a>';
                        Swal.fire({
                            icon: 'info',
                            title: 'Selected File(s)',
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: html,
                        });
                    });
                }
            });
            $('.root-cause-analysis-file-view').click(function() {
                if ($('.root-cause-analysis-file-upload input').length) {
                    $this = $('.root-cause-analysis-file-upload');
                    html = '';
                    $('.root-cause-analysis-file-upload input').each(function() {
                        let url = $(this).data('url');

                        // Force the URL to start with 'https://'
                        url = url.replace(/^http:\/\//i, 'https://');

						html += '<a href="'+url+'" target="_blank">'+$(this).data('title')+'</a>';
                        Swal.fire({
                            icon: 'info',
                            title: 'Selected File(s)',
                            allowOutsideClick: false,
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            html: html,
                        });
                    });
                }
            });
            /*end init*/
            $('#ncar-main_wrapper').on('click', '.paginate_button', function() {
                app.loadBtnAction();
            });
        },
        loadBtnAction: function() {
            $('#ncar-main_wrapper').on('click', '.btn-delete:not(.loaded)', function() {
                id = $(this).parents('tr').data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete this item?",
                    icon: 'warning',
                    showCancelButton: true,
                    allowOutsideClick: false,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        // Swal.close();
                        Swal.fire({
                            icon: 'info',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            allowEscapeKey: false,
                            html: '<p style="font-size: 12px;"> Loading request. Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                        });
                        $.ajax({
                            url: location.origin + '/wp-admin/admin-ajax.php',
                            data: {
                                action: 'ncar_delete',
                                id: id
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            success: function(r) {
                                Swal.close();
                                if (r.post_id) {
                                    $icon = 'success';
                                    $title = 'NCAR Deleted';
                                    $text = '';
                                } else {
                                    $icon = 'error';
                                    $title = 'NCAR Not Deleted';
                                    $text = 'Error occurred';
                                }
                                Swal.fire({
                                    icon: $icon,
                                    title: $title,
                                    allowOutsideClick: false,
                                    showConfirmButton: true,
                                    allowEscapeKey: false,
                                    html: $text,
                                }).then(function(result) {
                                    location.reload();
                                });
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    icon: 'info',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    allowEscapeKey: false,
                                    html: '<p style="font-size: 12px;"> Please wait...</p><i class="fa fa-refresh fa-spin"></i>',
                                });
                            }
                        });
                    } else {
                        Swal.close();
                    }
                });
            }).addClass('loaded');
            /*edit*/
            $('#ncar-main_wrapper').on('click', '.btn-edit:not(.loaded)', function() {
                $('#edit-modal .nav-tabs li:first-child a').click();
                id = $(this).parents('tr').data('id');
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_edit',
                        id: id
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        _correction_ind = 0;
                        $('#edit-modal').LoadingOverlay('hide');
                        /*form 1*/
                        $.each(r.data, function(i, v) {
                            $el = $('#edit-modal [name="' + i + '"]');
                            if (i == 'evidences') {
                                e_html = (v.length ? v.length + ' file(s) selected' : '');
                                e_input = '';
                                $.each(v, function(a, b) {
                                    e_input += '<input type="hidden" data-url="' + b.url + '" value="' + b.id + '" data-title="' + b.title + '" class="evidences">';
                                });
                                $('#edit-modal #part1 .file-group.evidences').html(e_input);
                                $('#edit-modal #part1 .selected_files').val(e_html);
                            } else if (i == 'source_of_nc') {
                                $('#edit-modal #part1 [name="' + i + '"][value="' + v + '"]').prop('checked', true);
                            } else if ($el.prop("tagName") == 'SELECT') {
                                $el.find('option[value="' + v + '"]').attr('selected', 'selected');
                            } else if ($el.prop("tagName") == 'INPUT') {
                                $el.val(v);
                            } else if ($el.prop("tagName") == 'TEXTAREA') {
                                $el.html(v);
                            }
                        });
                        /*form 2*/
                        $html2 = '';
                        $.each(r.form2.correction, function(i, v) {
                            if(v.correction_text  == undefined){
                                v.correction_text = 'remarks';
                            }

                            $html2 += '' + '<tr>' + '<td colspan="4"><textarea disabled class="form-control correction_text" rows="5">' + v.correction_text + '</textarea></td>' + '<td colspan="2"><input type="date" disabled class="form-control correction_date" value="' + v.correction_date + '"></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
                            _correction_ind++;

                            $('#form_2_1_b').html($html2);
                            app.bindDeleteBtns();
                        });


                        $html = '';
                        console.log(r);
                        if(r.data.source_of_nc == 'Improvement Potential'){
                            var readonly = 'readonly';
                        } else {
                            var readonly = '';
                        }




                        $.each(r.form2.corrective_action_data, function(i, v) {

                            let latestCorrectiveDate = null;
                            // Ensure corrective_date exists and is valid
                            if (v.corrective_date && !isNaN(Date.parse(v.corrective_date))) {
                                let correctiveDate = new Date(v.corrective_date);

                                console.log(correctiveDate);
                                // Update the latestCorrectiveDate if current date is later
                                if (!latestCorrectiveDate || correctiveDate > latestCorrectiveDate) {
                                    latestCorrectiveDate = correctiveDate;
                                }
                            }

                            if (latestCorrectiveDate) {
                                let currentDate = new Date();
                                let daysDifference = Math.floor((currentDate - latestCorrectiveDate) / (1000 * 60 * 60 * 24));

                                // Disable the button if 7 or more days have passed
                                if (daysDifference <= 7) {
                                    $('#edit_form2_save_satisfactory').prop('disabled', true);
                                } else {
                                    $('#edit_form2_save_satisfactory').prop('disabled', false);
                                }
                            }
                        });

                        if (!r.form2.correction || r.form2.correction.length === 0) {

                            if (readonly == 'readonly') {
                                var ph  = 'Not Applicable';
                            } else {
                                var ph  = '';
                            }
                            var today = new Date();
                            var defaultDate = today.toISOString().split('T')[0]; // Format date as YYYY-MM-DD

                            // Add a placeholder row when correction is empty
                            $html += '<tr>' +
                                        '<td colspan="5">' +
                                            '<textarea required class="form-control correction_text" ' + readonly + '>'+ph+'</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<input type="date" readonly class="form-control correction_date test" value="'+defaultDate+'">' +
                                        '</td>' +
                                        '<td>' +
                                            '<button class="close delete-correction"><span aria-hidden="true">×</span></button>' +
                                        '</td>' +
                                    '</tr>';
                        } else {
                            // Iterate and create rows for each correction


                            $.each(r.form2.correction, function(i, v) {
                                if (v.correction_text == undefined) {
                                    v.correction_text = '';
                                }
                                if (readonly == 'readonly') {
                                    v.correction_text = 'Not Applicable';
                                }



                                // Set default date to today if correction_date is undefined or empty
                                if (v.correction_date == undefined || v.correction_date == '') {
                                    var today = new Date();
                                    var defaultDate = today.toISOString().split('T')[0]; // Format date as YYYY-MM-DD
                                    v.correction_date = defaultDate;
                                }



                                $html += '<tr>' +
                                            '<td colspan="5">' +
                                                '<textarea required class="form-control correction_text" ' + readonly + '>' + v.correction_text + '</textarea>' +
                                            '</td>' +
                                            '<td>' +
                                                '<input type="date" readonly class="form-control correction_date test" value="' + v.correction_date + '">' +
                                            '</td>' +
                                            '<td>' +
                                                '<button class="close delete-correction"><span aria-hidden="true">×</span></button>' +
                                            '</td>' +
                                        '</tr>';
                                _correction_ind++;
                            });
                        }



                        $('#form_2_1').html($html);

                        // Adjust textarea heights based on content
                        jQuery('.correction_text').each(function() {
                            this.style.height = 'auto'; // Reset height to auto
                            let scrollHeight = this.scrollHeight;
                            let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                            let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                            let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                            let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                            // Adjust height by considering padding and border
                            this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                        });

                        app.bindDeleteBtns();

                        $html = '';
                        if (!r.form2.correction_rca || r.form2.correction_rca.length === 0) {
                            // Add a placeholder row when correction_rca is empty

                            if (readonly == 'readonly') {
                                var ph  = 'Not Applicable';
                            } else {
                                var ph  = '';
                            }

                            $html += '<tr class="rca">' +
                                        '<td colspan="5">' +
                                            '<textarea required class="form-control correction_text" ' + readonly + '>'+ph+'</textarea>' +
                                        '</td>' +
                                    '</tr>';
                        } else {
                            // Iterate and create rows for each correction_rca
                            $.each(r.form2.correction_rca, function(i, v) {
                                if (v.correction_text == undefined) {
                                    v.correction_text = '';
                                }
                                if (readonly == 'readonly') {
                                    v.correction_text = 'Not Applicable';
                                }

                                $html += '<tr class="rca">' +
                                            '<td colspan="5">' +
                                                '<textarea required class="form-control correction_text" ' + readonly + '>' + v.correction_text + '</textarea>' +
                                            '</td>' +
                                        '</tr>';
                                _correction_ind++;
                            });
                        }


                        $('#form_2_2 tr.rca').remove();
                        $('#form_2_2').append($html);

                        // Adjust textarea heights based on content
                        jQuery('.correction_text').each(function() {
                            this.style.height = 'auto'; // Reset height to auto
                            let scrollHeight = this.scrollHeight;
                            let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                            let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                            let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                            let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                            // Adjust height by considering padding and border
                            this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                        });

                        app.bindDeleteBtns();

                        $html2 = '';
                        $.each(r.form2.correction_rca, function(i, v) {
                            if(v.correction_text  == undefined){
                                v.correction_text = '';
                            }

                            $html2 += '<tr class="rca">' +
                                        '<td colspan="5">' +
                                            '<textarea disabled class="form-control correction_text">' + v.correction_text + '</textarea>' +
                                        '</td>' +
                                    '</tr>';
                            _correction_ind++;
                        });

                        $('#form_2_2_b tr.rca').remove();
                        $('#form_2_2_b').append($html2);

                        // Adjust textarea heights based on content
                        jQuery('.correction_text').each(function() {
                            this.style.height = 'auto'; // Reset height to auto
                            let scrollHeight = this.scrollHeight;
                            let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                            let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                            let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                            let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                            // Adjust height by considering padding and border
                            this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                        });

                        app.bindDeleteBtns();


                        e_html = (r.form2.files.length ? r.form2.files.length + ' file(s) selected' : '');
                        e_input = '';
                        $.each(r.form2.files, function(a, b) {
                            e_input += '<input type="hidden" data-url="' + b.url + '" value="' + b.id + '" data-title="' + b.title + '" class="evidences">';
                        });
                        $('#edit-modal #part2 .file-group').html(e_input);
                        $('#edit-modal #part2 .selected_files').val(e_html);

                        $html = '';
                        $html2 = '';
                        $.each(r.form2.corrective_action_data, function(i, v) {
                            if(v.root_causes == undefined){
                                v.root_causes = '';
                            }

                            if(v.corrective_action == undefined){
                                v.corrective_action = '';
                            }

                            if (readonly == 'readonly') {
                                v.root_causes = 'Not Applicable';
                            }

                            $html2 += '<tr>' +
                                        '<td>' +
                                            '<textarea disabled class="form-control root_causes" '+readonly+'>' + v.root_causes + '</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<textarea disabled class="form-control corrective_action">' + v.corrective_action + '</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<input type="date" disabled class="form-control corrective_date" value="' + v.corrective_date + '">' +
                                        '</td>' +
                                        '<td style="display:flex;">' +
                                            '<input type="radio" name="corrective_' + _correction_ind + '" class="corrective_implemented" value="Yes" ' + (v.corrective_implemented == 'Yes' ? 'checked' : '') + '> Yes' +
                                            '<input  style="margin-left:5px;" type="radio" name="corrective_' + _correction_ind + '" class="corrective_implemented" value="No" ' + (v.corrective_implemented == 'No' ? 'checked' : '') + '> No' +
                                        '</td>' +
                                        '<td>' +
                                            '<textarea class="form-control input-sm corrective_remarks" placeholder="remarks" rows="3">' + v.corrective_remarks + '</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<button class="close delete-correction"><span aria-hidden="true">×</span></button>' +
                                        '</td>' +
                                    '</tr>';

                            $html += '<tr>' +
                                        '<td colspan="4">' +
                                            '<textarea required class="form-control root_causes" '+readonly+'>' + v.root_causes + '</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<textarea required class="form-control corrective_action">' + v.corrective_action + '</textarea>' +
                                        '</td>' +
                                        '<td>' +
                                            '<input type="date" class="form-control corrective_date" value="' + v.corrective_date + '">' +
                                        '</td>' +
                                        '<td>' +
                                            '<button class="close delete-correction"><span aria-hidden="true">×</span></button>' +
                                        '</td>' +
                                    '</tr>';

                            _correction_ind++;
                        });

                        $('#form_2_3_b').html($html2);
                        $('#form_2_3').html($html);

                        // Adjust textarea heights based on content
                        $('.root_causes, .corrective_action').each(function() {
                            this.style.height = 'auto'; // Reset height to auto
                            let scrollHeight = this.scrollHeight;
                            let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                            let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                            let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                            let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                            // Adjust height by considering padding and border
                            this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                        });

                        app.bindDeleteBtns();


                        jQuery('.view-button').click(function(){
                            content = jQuery(this).data('content');
                            alert(content);
                        });
                        /*end*/
                        /*form 3*/
                        $html = '';
                        $.each(r.form3.verification, function(i, v) {
                            $html += '' + '<tr>' + '<td colspan="3">' + '<input type="radio" name="verification_' + _correction_ind + '" class="verification_implemented hidden" value="Yes" ' + (v.verification_implemented == 'Yes' ? 'checked' : '') + '> ' + '<input type="radio" name="verification_' + _correction_ind + '" class="verification_implemented hidden" value="No" ' + (v.verification_implemented == 'No' ? 'checked' : '') + '>' + '<textarea class="form-control input-sm verification_remarks" placeholder="remarks" rows="3">' + v.verification_remarks + '</textarea></td>' + '' + '<td><input type="date" class="form-control input-sm verification_date" value="' + v.verification_date + '"></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
                            _correction_ind++;
                            $('#form_3_1').html($html);
                            app.bindDeleteBtns();
                        });
                        /*end*/
                        /*modal restriction*/
                        if (r.cant_edit) {
                            $('#edit-modal #part1').addClass('readonly');
                        } else {
                            $('#edit-modal #part1').removeClass('readonly');
                        }
                        if (r.cant_review) {
                            $('#edit-modal #part2').addClass('readonly');
                        } else {
                            $('#edit-modal #part2').removeClass('readonly');
                        }
                        if (r.cant_approve) {
                            $('#edit-modal #part3').addClass('readonly');
                        } else {
                            $('#edit-modal #part3').removeClass('readonly');
                            $('#edit-modal #part2b').removeClass('readonly');
                            $('#edit-modal #part2b').find('.submit-group').css('display', 'none');
                        }

                        if (r.cant_followup) {
                            if (r.cant_approve) {
                                $('#edit-modal #part2b').addClass('readonly');
                            } else {
                                $('#edit-modal #part2b').removeClass('readonly');
                            }
                        } else {
                            $('#edit-modal #part2b').removeClass('readonly');
                            $('#edit-modal #part2b').find('.submit-group').css('display', 'block');
                        }

                        jQuery('.root_causes, .corrective_action, .correction_text').each(function() {
                            this.style.height = 'auto'; // Reset height to auto
                            let scrollHeight = this.scrollHeight;
                            let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                            let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                            let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                            let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                            // Adjust height by considering padding and border
                            this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                        });

                    },
                    beforeSend: function() {
                        $('#edit-modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).LoadingOverlay('show');
                    },
                });
            }).addClass('loaded');
            /*remarks*/
            $('.btn-remarks:not(.loaded)').on('click', function() {
                id = $(this).parents('tr').data('id');
                $.ajax({
                    url: location.origin + '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'ncar_load_remarks',
                        id: id
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(r) {
                        _correction_ind = 0;
                        $('#remarks-modal').LoadingOverlay('hide');
                        /*form 1*/
                        $('#remarks-modal input[name="ncar_no"]').val(r.data.ncar_no);
                        $('#remarks-modal #ncar_remarks').val(r.data.remarks);
                    },
                    beforeSend: function() {
                        $('#remarks-modal').modal({
                            backdrop: 'static',
                            keyboard: false
                        }).LoadingOverlay('show');
                    },
                });
            }).addClass('loaded');
        }
    }
    $(document).ready(function() {
        app.init();
        app.loadBtnAction();

        jQuery('li[role="presentation"]').click(function(){
            setTimeout(function() {
                jQuery('.root_causes, .corrective_action, .correction_text').each(function() {
                    this.style.height = 'auto'; // Reset height to auto
                    let scrollHeight = this.scrollHeight;
                    let computedStyle = window.getComputedStyle(this); // Get the computed style of the textarea
                    let paddingTop = parseFloat(computedStyle.paddingTop); // Get the padding from the computed style
                    let paddingBottom = parseFloat(computedStyle.paddingBottom); // Get the padding from the computed style
                    let borderHeight = parseFloat(computedStyle.borderTopWidth) + parseFloat(computedStyle.borderBottomWidth); // Get the border height from the computed style

                    // Adjust height by considering padding and border
                    this.style.height = (scrollHeight + paddingTop + paddingBottom + borderHeight) + 'px';
                });
            }, 2000 ); // Adjust the timeout value (in milliseconds) as per your requirement
        });
    });
})(jQuery)