(function($){

	var _correction_ind = 0;

	var app = {
		bindDeleteBtns: function() {
			$('.delete-correction:not(.loaded)').each(function(){
				$(this).addClass('loaded');
				$(this).click(function(){
					$(this).parents('tr').remove();
				});
			});
		},
		init: function() {
			$('#ncar-main').DataTable({
				order: [[0, 'desc']],
			});

			$('#btn-add').click(function(){
				$('#add-modal').modal({
				    backdrop: 'static',
				    keyboard: false
				});
			});

			$('[data-toggle="tooltip"]').tooltip();
			$('form').on('submit', function(e){
				e.preventDefault();
			});

			$('#main_form_save').on('click', function(e){
				e.preventDefault();

				/*files*/
				_files = [];
				$('#ncar_main_form .evidences input').each(function(){

					_files.push( $(this).val() );

				});
				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_save',
						data: $('#ncar_main_form').serializeArray(),
						evidences: _files
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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

			$('#edit_form_save').on('click', function(e){
				e.preventDefault();

				/*files*/
				_files = [];
				$('#ncar_edit_form .evidences input').each(function(){

					_files.push( $(this).val() );

				});
				ncar_no = $('#edit-modal [name="ncar_no"]').val();
				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_edit_save',
						data: $('#ncar_edit_form').serializeArray(),
						evidences: _files,
						ncar_no: ncar_no
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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

			$('#add_correction').click(function(){
				date = new Date;

				$html = ''+
				'<tr>'+
					'<td colspan="2"><textarea class="form-control correction_text" rows="5"></textarea></td>'+
					'<td><input type="date" class="form-control target_date" value="'+date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate()+'"></td>'+
					'<td><input type="date" class="form-control correction_date" value="'+date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate()+'"></td>'+
					'<td class="hidden" style="display:inline-flex;">'+
						'<input type="radio" name="correction_implemented_'+_correction_ind+'" class="correction_implemented" value="Yes"> Yes'+
						'<input type="radio" style="margin-left: 10px;" name="correction_implemented_'+_correction_ind+'" class="correction_implemented" value="No"> No'+
					'</td>'+
					'<td><div class="form-group file-upload" data-id="'+_correction_ind+'">' +
					'<label for="evidences"><button type="" class="btn btn-info btn-sm upload-btn-new" data-id="'+_correction_ind+'">Select files</button></label>' +
					'<div class="hidden file-group evidences" id="improvement-action-'+_correction_ind+'"></div>' +
					'<input type="text" readonly class="selected_files form-control" id="improvement-action-file-'+_correction_ind+'" value="">' +
					'</div></td>'+
					'<td class="hidden"><input type="text" class="form-control input-sm correction_remarks" placeholder="remarks"></td>'+
					'<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>'+
				'</tr>';

				_correction_ind++;
				$('#form_2_1').append( $html );
				app.bindDeleteBtns();
			});

			/*corrective action*/
			$('#add_corrective_action').click(function(){
				date = new Date;

				$html = ''+
				'<tr>'+
					'<td><input type="text" class="form-control root_causes"></td>'+
					'<td><input type="text" class="form-control corrective_action"></td>'+
					'<td><input type="date" class="form-control corrective_date" value="'+date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate()+'"></td>'+
					'<td>'+
						'<input type="radio" name="corrective_'+_correction_ind+'" class="corrective_implemented" value="Yes"> Yes'+
						'<input type="radio" name="corrective_'+_correction_ind+'" class="corrective_implemented" value="No"> No'+
					'</td>'+
					'<td><input type="text" class="form-control input-sm corrective_remarks" placeholder="remarks"></td>'+
					'<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>'+
				'</tr>';

				_correction_ind++;
				$('#form_2_3').append( $html );
				app.bindDeleteBtns();
			});

			$('#edit_form2_save').click(function(e){
				e.preventDefault();
				correction = [];
				ncar_no = $('#edit-modal [name="ncar_no"]').val();
				$('#form_2_1 tr').each(function(){
					correction_text = $(this).find('.correction_text').val();
					target_date = $(this).find('.target_date').val();
					correction_date = $(this).find('.correction_date').val();
					correction_implemented = ( $(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '' );
					correction_remarks = $(this).find('.correction_remarks').val();
					correction_attachment_url = $(this).find('.evidences ').find('input').data('url');
					correction_attachment_id = $(this).find('.evidences ').find('input').val();
					correction_attachment_title = $(this).find('.evidences ').find('input').data('title');

					correction.push({
						correction_text: correction_text,
						target_date: target_date,
						correction_date: correction_date,
						correction_implemented: correction_implemented,
						correction_remarks: correction_remarks,
						correction_attachment_url: correction_attachment_url,
						correction_attachment_id: correction_attachment_id,
						correction_attachment_title: correction_attachment_title,
					});

				});

				corrective_action_data = [];
				$('#form_2_3 tr').each(function(){
					root_causes = $(this).find('.root_causes').val();
					corrective_action = $(this).find('.corrective_action').val();
					corrective_date = $(this).find('.corrective_date').val();
					corrective_implemented = ( $(this).find('.corrective_implemented:checked') ? $(this).find('.corrective_implemented:checked').val() : '' );
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
				$('#part2 #form_2_2 .file-group input').each(function(){
					files.push( $(this).val() );
				});
				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_form2_save',
						data: {
							correction: correction,
							files: files,
							corrective_action_data: corrective_action_data,
							ncar_no: ncar_no
						},
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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
			jQuery('#ncar_main_form [name="source_of_nc"]').on('click', function(){
				source_of_nc = jQuery(this).val();
				if(source_of_nc == 'Others'){
					jQuery('.other_source').css('display', 'block');
				} else {
					jQuery('.other_source').css('display', 'none');
				}
			});

			jQuery('#ncar_edit_form [name="source_of_nc"]').on('click', function(){
				source_of_nc = jQuery(this).val();
				if(source_of_nc == 'Others'){
					jQuery('.other_source').css('display', 'block');
				} else {
					jQuery('.other_source').css('display', 'none');
				}
			});

			
			
			$('#edit_form3_save_satisfactory').click(function(e){
				e.preventDefault();
                correction = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1 tr').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_date = $(this).find('.correction_date').val();
                    correction_implemented = ($(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '');
                    correction_remarks = $(this).find('.correction_remarks').val();
                    correction.push({
                        correction_implemented: correction_implemented,
                        correction_remarks: correction_remarks,
                    });
                });

				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_form3_save',
						data: {
							correction: correction,
                            ncar_no: ncar_no,
                            satisfactory: 1
						},
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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

			$('#edit_form3_save_not_satisfactory').click(function(e){
				e.preventDefault();
                correction = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1 tr').each(function() {
                    correction_text = $(this).find('.correction_text').val();
                    correction_date = $(this).find('.correction_date').val();
                    correction_implemented = ($(this).find('.correction_implemented:checked') ? $(this).find('.correction_implemented:checked').val() : '');
                    correction_remarks = $(this).find('.correction_remarks').val();
                    correction.push({
                        correction_implemented: correction_implemented,
                        correction_remarks: correction_remarks,
                    });
                });

				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_form3_save',
						data: {
							correction: correction,
                            ncar_no: ncar_no,
                            satisfactory: 0
						},
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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

			$('#add_verification').click(function() {
				date = new Date;
				$html = '' +
					'<tr>' +
						'<td colspan="3">' +
							'<input type="hidden" name="verification_' + _correction_ind + '" class="verification_implemented" value="Yes" style="display: none;">' +
							'<input type="hidden" name="verification_' + _correction_ind + '" class="verification_implemented" value="No" style="display: none;">' +
						'' +
						'<input type="text" class="form-control input-sm verification_remarks" placeholder="remarks"></td>' +
						'<td><input type="date" class="form-control input-sm verification_date" value="' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + '"></td>' +
						'<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' +
					'</tr>';
				_correction_ind++;
				$('#form_3_1_b').append($html);
				app.bindDeleteBtns();
			});
			

			$('#edit_form3b_save_satisfactory').click(function(e) {
                e.preventDefault();
                verification = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1_b tr').each(function() {
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
                        action: 'ncar_ia_form3b_save',
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
            $('#edit_form3b_save_not_satisfactory').click(function(e) {
                e.preventDefault();
                verification = [];
                ncar_no = $('#edit-modal [name="ncar_no"]').val();
                $('#form_3_1_b tr').each(function() {
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
                        action: 'ncar_ia_form3b_save',
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
			$('#save_remarks').click(function(e){
				e.preventDefault();
				verification = [];
				ncar_no = $('#remarks-modal [name="ncar_no"]').val();
				remarks = $('#remarks-modal #ncar_remarks').val();

				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_save_remarks',
						data: {
							remarks: remarks,
							ncar_no: ncar_no
						},
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						Swal.close();
						if ( r.post_id ) {

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
						}).then( function(result) {
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
				multiple = ( $this.data('multiple-upload') ? true : false );

			    if ( typeof _uploader != 'undefined' ) {
			      _uploader.open();
			      return;
			    }

				_uploader = wp.media( {
					title: 'Upload Files',
					multiple: multiple
				} ).on( 'select', function(e){
					$('body').addClass('modal-open');
					/** This will return the selected image from the featured-image Uploader, the result is an object */
					uploaded_files = _uploader.state().get( 'selection' );
					uploaded_files = uploaded_files.toJSON();
					list = '';
					uploaded_files.forEach(function(v, i){
						list += '<input type="hidden" data-url="'+v.url+'" value="'+v.id+'" data-title="'+v.title+'" class="evidences">';
					});
					$this.find('.file-group').html( list );

					if ( list ) {
						$this.find('.selected_files').val( uploaded_files.length + ' file(s) selected' );
					}
				});

				_uploader.on('open', function(){
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
				_uploader.on('close', function(){
					$('body').addClass('modal-open');
				});
				_uploader.open();
			});


			$('#ncar_edit_form2').on( 'click', '.file-upload .upload-btn-new', function() {
				$this = $(this).parents('.file-upload');
				multiple = false;

			    if ( typeof _uploader != 'undefined' ) {
			      _uploader.open();
			      return;
			    }

				_uploader = wp.media( {
					title: 'Upload Files',
					multiple: multiple
				} ).on( 'select', function(e){
					$('body').addClass('modal-open');
					/** This will return the selected image from the featured-image Uploader, the result is an object */
					uploaded_files = _uploader.state().get( 'selection' );
					uploaded_files = uploaded_files.toJSON();
					list = '';
					uploaded_files.forEach(function(v, i){
						list += '<input type="hidden" data-url="'+v.url+'" value="'+v.id+'" data-title="'+v.title+'" class="evidences">';
					});
					$this.find('.file-group').html( list );

					if ( list ) {
						$this.find('.selected_files').val( uploaded_files.length + ' file selected' );
					}
				});

				_uploader.on('open', function(){
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
				_uploader.on('close', function(){
					$('body').addClass('modal-open');
				});
				_uploader.open();
			});

			/*view evidences*/

			$('#ncar_edit_form2').on('click', '.view-btn-new', function(){
				if ( $(this).parent().find('.evidences').length ) {
					$this = $(this).parent().find('.evidences');
					html = '';
					$this.each(function(){
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
			$('#ncar_edit_form3').on('click', '.view-btn-new', function(){
				if ( $(this).parent().find('.evidences').length ) {
					$this = $(this).parent().find('.evidences');
					html = '';
					$this.each(function(){

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

			$('#ncar_edit_form2').on('click', '.selected_files', function(){
				if ( $('.evidences input').length ) {
					$this = $(this).parents('.file-upload');
					html = '';
					$this.find('.evidences input').each(function(){

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
			
			$('#ncar_edit_form3').on('click', '.selected_files', function(){
				if ( $('.evidences input').length ) {
					$this = $(this).parents('.file-upload');
					html = '';
					$this.find('.evidences input').each(function(){

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
		},
		loadBtnAction: function() {
			$('.btn-delete:not(.loaded)').on('click', function(){

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
				}).then( function(result) {
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
								action: 'ncar_ia_delete',
								id: id
							},
							type: 'POST',
							dataType: 'JSON',
							success: function(r) {
								Swal.close();
								if ( r.post_id ) {

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
								}).then( function(result) {
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
						action: 'ncar_ia_edit',
						id: id
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						_correction_ind = 0;
						$('#edit-modal').LoadingOverlay('hide');
						/*form 1*/
						$.each(r.data, function(i, v) {
							$el = $('#edit-modal [name="'+i+'"]');
							if ( i == 'evidences' ) {

								e_html = ( v.length ? v.length + ' file(s) selected' : '' ) ;
								e_input = '';
								$.each(v, function(a, b) {
									e_input += '<input type="hidden" data-url="'+b.url+'" value="'+b.id+'" data-title="'+b.title+'" class="evidences">';
								});

								$('#edit-modal #part1 .file-group.evidences').html( e_input );
								$('#edit-modal #part1 .selected_files').val( e_html );

							} else if ( i == 'source_of_nc' ) {
								$('#edit-modal #part1 [name="'+i+'"][value="'+v+'"]').prop('checked', true);
							} else if ( i == 'other_source' ) {
								if(v){
									$('#edit-modal #part1 [name="'+i+'"]').css('display', 'block');
									$('#edit-modal #part1 [name="'+i+'"]').val(v);
								} else {
									$('#edit-modal #part1 [name="'+i+'"]').css('display', 'none');
									$('#edit-modal #part1 [name="'+i+'"]').val(v);
								}
							}else if ( i == 'ncar_ia' ) {
								$('#edit-modal #part1 [name="'+i+'"]').val(v);
							} else if ( $el.prop("tagName") == 'SELECT' ) {
								$el.find('option[value="'+v+'"]').attr('selected', 'selected');
							} else if ( $el.prop("tagName") == 'INPUT' ) {
								$el.val(v);
							}  else if ( $el.prop("tagName") == 'TEXTAREA' ) {
								$el.html(v);
							}



						});

						/*form 2*/
						$html = '';
						$.each(r.form2.correction, function(i, v) {

							console.log(v);
							e_html_attachment = (v.correction_attachment_url ? '1 file selected' : '');
							e_input_attachment = (v.correction_attachment_url ? '<input type="hidden" data-url="' + v.correction_attachment_url + '" value="' + v.correction_attachment_id + '" data-title="' + v.correction_attachment_title + '" class="evidences">' : '');

							console.log(e_input_attachment);
							$html += ''+
							'<tr>'+
								'<td colspan="2"><textarea class="form-control correction_text">' + v.correction_text + '</textarea></td>'+
								'<td><input type="date" class="form-control target_date" value="'+v.target_date+'"></td>'+
								'<td><input type="date" class="form-control correction_date" value="'+v.correction_date+'"></td>'+
								'<td class="hidden" style="display:inline-flex;">'+
									'<input type="radio" name="correction_implemented_'+_correction_ind+'" class="correction_implemented" value="Yes" '+( v.correction_implemented == 'Yes' ? 'checked' : '' )+'> Yes'+
									'<input type="radio" style="margin-left: 10px;" name="correction_implemented_'+_correction_ind+'" class="correction_implemented" value="No" '+( v.correction_implemented == 'No' ? 'checked' : '' )+'> No'+
								'</td>'+
								'<td><button class="btn btn-info btn-sm view-btn-new">View saved File</button>'+e_input_attachment+'</td>'+
								'<td><div class="form-group file-upload" data-id="'+_correction_ind+'">' +
								'<label for="evidences"><button type="" class="btn btn-info btn-sm upload-btn-new" data-id="'+_correction_ind+'">Select New file</button></label>' +
								'<div class="hidden file-group evidences" id="improvement-action-'+_correction_ind+'">'+e_input_attachment+'</div>' +
								'<input type="text" readonly class="selected_files form-control" id="improvement-action-file-'+_correction_ind+'" value="'+e_html_attachment+'">' +
								'</div></td>'+
								'<td class="hidden"><input type="text" class="form-control input-sm correction_remarks" placeholder="remarks" value="'+v.correction_remarks+'"></td>'+
								'<td></td>'+
							'</tr>';

							_correction_ind++;
							$('#form_2_1').html( $html );
							app.bindDeleteBtns();
						});

						e_html = ( r.form2.files.length ? r.form2.files.length + ' file(s) selected' : '' ) ;
						e_input = '';
						$.each(r.form2.files, function(a, b) {
							e_input += '<input type="hidden" data-url="'+b.url+'" value="'+b.id+'" data-title="'+b.title+'" class="evidences">';
						});

						$('#edit-modal #part2 .file-group').html( e_input );
						$('#edit-modal #part2 .selected_files').val( e_html );

						$html = '';
						$.each(r.form2.corrective_action_data, function(i, v) {
							$html += ''+
							'<tr>'+
								'<td><input type="text" class="form-control root_causes" value="'+v.root_causes+'"></td>'+
								'<td><input type="text" class="form-control corrective_action" value="'+v.corrective_action+'"></td>'+
								'<td><input type="date" class="form-control corrective_date" value="'+v.corrective_date+'"></td>'+
								'<td>'+
									'<input type="radio" name="corrective_'+_correction_ind+'" class="corrective_implemented" value="Yes" '+( v.corrective_implemented == 'Yes' ? 'checked' : '' )+'> Yes'+
									'<input type="radio" name="corrective_'+_correction_ind+'" class="corrective_implemented" value="No" '+( v.corrective_implemented == 'No' ? 'checked' : '' )+'> No'+
								'</td>'+
								'<td><input type="text" class="form-control input-sm corrective_remarks" placeholder="remarks" value="'+v.corrective_remarks+'"></td>'+
								'<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>'+
							'</tr>';

							_correction_ind++;
							$('#form_2_3').html( $html );
							app.bindDeleteBtns();
						});
						/*end*/
						/*form 3*/
						$html = '';
						$html2 = '';
						$.each(r.form2.correction, function(i, v) {
							if(v.correction_text  == undefined){
								v.correction_text = 'remarks';
							}
							
							e_html_attachment = (v.correction_attachment_url ? ' 1 file selected' : '');
							e_input_attachment = (v.correction_attachment_url ? '<input type="hidden" data-url="' + v.correction_attachment_url + '" value="' + v.correction_attachment_id + '" data-title="' + v.correction_attachment_title + '" class="evidences">' : '');
							
							$html2 += '' + '<tr>' + '<td colspan="1">' +
							 '<input type="text" disabled class="form-control  correction_text" value="' + v.correction_text + '"><button class="btn btn-primary view-button" data-content="' + v.correction_text + '">view</button></td>' + 
							 '<td><input type="date" disabled class="form-control target_date" value="' + v.target_date + '"></td>' + '<td><input type="date" disabled class="form-control correction_date" value="' + v.correction_date + '"></td>' +
							 '<td><button class="btn btn-info btn-sm view-btn-new">View File</button>'+e_input_attachment+'</td></td>'+
							 '<td style="display:inline-flex;">' + '<input type="radio" name="correction_implemented_' + _correction_ind + '" class="correction_implemented" value="Yes" ' + (v.correction_implemented == 'Yes' ? 'checked' : '') + '> Yes' + '<input type="radio" style="margin-left: 10px;" name="correction_implemented_' + _correction_ind + '" class="correction_implemented" value="No" ' + (v.correction_implemented == 'No' ? 'checked' : '') + '> No' + '</td>' + '<td colspan="2"><input type="text" class="form-control input-sm correction_remarks" placeholder="remarks" value="' + v.correction_remarks + '"><button class="btn btn-primary view-button" data-content="' + v.correction_remarks + '">view</button></td>' + '<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' + '</tr>';
							_correction_ind++;
							
							$('#form_3_1').html($html2);
							app.bindDeleteBtns();
						});
						/*end*/

						/*form 3*/
                        $html = '';
						$.each(r.form3.verification, function(i, v) {
							$html += '' +
								'<tr>' +
									'<td colspan="3">' +
										'<input type="hidden" name="verification_' + _correction_ind + '" class="verification_implemented" value="Yes" ' + (v.verification_implemented == 'Yes' ? 'checked' : '') + ' style="display: none;"> ' +
										'<input type="hidden" name="verification_' + _correction_ind + '" class="verification_implemented" value="No" ' + (v.verification_implemented == 'No' ? 'checked' : '') + ' style="display: none;"> ' +
									'' +
									'<input type="text" class="form-control input-sm verification_remarks" placeholder="remarks" value="' + v.verification_remarks + '"></td>' +
									'<td><input type="date" class="form-control input-sm verification_date" value="' + v.verification_date + '"></td>' +
									'<td><button class="close delete-correction"><span aria-hidden="true">×</span></button></td>' +
								'</tr>';
							_correction_ind++;
							$('#form_3_1_b').html($html);
							app.bindDeleteBtns();
						});

						jQuery('.view-button').click(function(){
                            content = jQuery(this).data('content');
                            alert(content);
                        });

						/*modal restriction*/
						if ( r.cant_edit ) {
							$('#edit-modal #part1').addClass( 'readonly' );
						} else {
							$('#edit-modal #part1').removeClass( 'readonly' );
						}

						if ( r.cant_review ) {
							$('#edit-modal #part2').addClass( 'readonly' );
						} else {
							$('#edit-modal #part2').removeClass( 'readonly' );
						}

						if ( r.cant_approve ) {
							$('#edit-modal #part3').addClass( 'readonly' );
						} else {
							$('#edit-modal #part3').removeClass( 'readonly' );
						}
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
			$('.btn-remarks:not(.loaded)').on('click', function(){

				id = $(this).parents('tr').data('id');
				$.ajax({

					url: location.origin + '/wp-admin/admin-ajax.php',
					data: {
						action: 'ncar_ia_load_remarks',
						id: id
					},
					type: 'POST',
					dataType: 'JSON',
					success: function(r) {
						_correction_ind = 0;
						$('#remarks-modal').LoadingOverlay('hide');
						/*form 1*/
						$('#remarks-modal input[name="ncar_no"]').val( r.data.ncar_no );
						$('#remarks-modal #ncar_remarks').val( r.data.remarks );
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

	$(document).ready(function(){
		app.init();
		app.loadBtnAction();
	});

})(jQuery)