<?php
$order_id = $_GET['order_id'];
$page = 'load';

require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

$exists  = get_post_meta( $order_id, '_redypagos_trans_date', true ) != '' ? true : false;

if ( empty( get_post_meta( $order_id, '_redypagos_approved', true ) ) ) {
    $isApproved = false;
}else if ( get_post_meta( $order_id, '_redypagos_approved', true ) == 'no' ) {
    $isApproved = false;
}else if ( get_post_meta( $order_id, '_redypagos_approved', true ) == 'yes' ) {
    $isApproved = true;
}

if( $exists || $isApproved)
  $page = 'deprecated';

if(isset($_POST['submit-button'])){
    if($_FILES['fileUpload']['name'] != '' && $_POST['_redypagos_reference'] != '' && $_POST['_redypagos_trans_date'] != '' && $_POST['_redypagos_bank'] != ''){
        $uploadedfile = $_FILES['fileUpload'];
        $upload_overrides = array( 'test_form' => false );

        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        $imageurl = "";
        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $imageurl = $movefile['url'];

            $order = new WC_Order( $order_id );
            update_post_meta( $order_id, '_redypagos_img_payment', $imageurl );
            update_post_meta( $order_id, '_redypagos_reference', $_POST['_redypagos_reference'] );
            update_post_meta( $order_id, '_redypagos_trans_date', $_POST['_redypagos_trans_date'] );
            update_post_meta( $order_id, '_redypagos_bank', $_POST['_redypagos_bank'] );

            $page = 'loaded';
        } else {
            $page = 'error';
        }
    }
}

?>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Enviar comprobante de Redypagos</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
        <style>
            
            @import url(https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css);
            @import url("https://fonts.googleapis.com/css?family=Roboto");
            
            html, body, * {
                box-sizing: border-box;
                font-size: 16px;
            }
            html, body {
                height: 100%;
                text-align: center;
            }
            body {
                padding: 2rem;
                background: #f8f8f8;
            }
            h2 {
                font-family: "Roboto", sans-serif;
                font-size: 26px;
                line-height: 1;
                color: #454cad;
                margin-bottom: 0;
            }
            p {
                font-family: "Roboto", sans-serif;
                font-size: 18px;
                color: #5f6982;
            }
            .uploader {
                display: block;
                clear: both;
                margin: 0 auto;
                width: 100%;
                max-width: 600px;
            }
            .uploader label {
                float: left;
                clear: both;
                width: 100%;
                padding: 2rem 1.5rem;
                text-align: center;
                background: #fff;
                border-radius: 7px;
                border: 3px solid #eee;
                transition: all .2s ease;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            .uploader label:hover {
                border-color: #454cad;
            }
            .uploader label.hover {
                border: 3px solid #454cad;
                box-shadow: inset 0 0 0 6px #eee;
            }
            .uploader label.hover #start i.fa {
                -webkit-transform: scale(0.8);
                transform: scale(0.8);
                opacity: 0.3;
            }
            .uploader #start {
                float: left;
                clear: both;
                width: 100%;
            }
            .uploader #start.hidden {
                display: none;
            }
            .uploader #start i.fa {
                font-size: 50px;
                margin-bottom: 1rem;
                transition: all .2s ease-in-out;
            }
            .uploader #response {
                float: left;
                clear: both;
                width: 100%;
            }
            .uploader #response.hidden {
                display: none;
            }
            .uploader #response #messages {
                margin-bottom: .5rem;
            }
            .uploader #file-image {
                display: inline;
                margin: 0 auto .5rem auto;
                width: auto;
                height: auto;
                max-width: 180px;
            }
            .uploader #file-image.hidden {
                display: none;
            }
            .uploader #notimage {
                display: block;
                float: left;
                clear: both;
                width: 100%;
            }
            .uploader #notimage.hidden {
                display: none;
            }
            .uploader .progress {
                display: inline;
                clear: both;
                margin: 0 auto;
                width: 100%;
                max-width: 180px;
                height: 8px;
                border: 0;
                border-radius: 4px;
                background-color: #eee;
                overflow: hidden;
            }
            .uploader .progress[value]::-webkit-progress-bar {
                border-radius: 4px;
                background-color: #eee;
            }
            .uploader .progress[value]::-webkit-progress-value {
                background: linear-gradient(to right, #393f90 0%, #454cad 50%);
                border-radius: 4px;
            }
            .uploader .progress[value]::-moz-progress-bar {
                background: linear-gradient(to right, #393f90 0%, #454cad 50%);
                border-radius: 4px;
            }
            .uploader input[type="file"] {
                display: none;
            }
            .uploader div {
                margin: 0 0 .5rem 0;
                color: #5f6982;
            }
            .uploader .btn {
                display: inline-block;
                margin: .5rem .5rem 1rem .5rem;
                clear: both;
                font-family: inherit;
                font-weight: 700;
                font-size: 14px;
                text-decoration: none;
                text-transform: initial;
                border: none;
                border-radius: .2rem;
                outline: none;
                padding: 0 1rem;
                height: 36px;
                line-height: 36px;
                color: #fff;
                transition: all 0.2s ease-in-out;
                box-sizing: border-box;
                background: #454cad;
                border-color: #454cad;
                cursor: pointer;
            }
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.2/moment.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js" type="text/javascript"></script>
        <script>
        window.console = window.console || function(t) {};
        </script>
        <script>
        if (document.location.search.match(/type=embed/gi)) {
            window.parent.postMessage("resize", "*");
        }
        </script>
    </head>
    <body translate="no">

        <?php if( $page == 'load' ){ ?>

            <h2>Enviar Comprobante de RedyPagos</h2>

            <form id="file-upload-form" class="uploader" method="post" action="" enctype="multipart/form-data">
                <div class="row my-3 text-left">
                    <div class="col-md-8">
                        <span>Número de Referencia / Comprobante</span><br>
                        <input id="_redypagos_reference" class="form-control" type="text" name="_redypagos_reference" required>
                    </div>
                    <div class="col-md-4">
                        <span>Banco Emisor</span><br>
                        <input id="_redypagos_bank" class="form-control" type="text" name="_redypagos_bank" required>
                    </div>
                    <div class="col-md-4">
                        <span>Fecha de Transacción</span>
                        <input type="text" name="_redypagos_trans_date" id="_redypagos_trans_date" class="form-control" required>
                        <script type="text/javascript">
                            $.fn.datepicker.dates['es'] = {
                                days: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
                                daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
                                daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                                months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                                monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                                today: "Hoy",
                                clear: "Limpiar",
                                format: "dd/mm/yyyy",
                                weekStart: 0
                            };
                            $('#_redypagos_trans_date').datepicker({
                            language: 'es'
                            });
                        </script>
                    </div>
                </div>

                <input id="file-upload" type="file" name="fileUpload" accept="image/*">
                <label for="file-upload" id="file-drag">
                    <img id="file-image" src="#" alt="Preview" class="hidden">
                    <div id="start">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <div>Seleccione un archivo o arrastrelo aquí</div>
                        <div id="notimage" class="hidden">Seleccione una imagen</div>
                        <span id="file-upload-btn" class="btn btn-primary">Seleccione un archivo</span>
                    </div>
                    <div id="response" class="hidden">
                        <div id="messages"></div>
                    </div>
                </label>
                <button type="submit" name="submit-button" class="btn btn-primary" id="submit-button">Enviar comprobante</button>
            </form>
        <?php }elseif( $page == 'loaded' ){ ?>
            <div class="uploader">
                <label>
                    <span style="font-size:78px">
                    <i class="fa fa-check-circle fa-2x text-success"></i>
                    <div>Se subió el archivo correctamente!</div>
                    </span>
                </label>
            </div>
        <?php }elseif( $page == 'error' ){ ?>

            <div class="uploader">
                <label>
                    <span style="font-size:78px">
                    <i class="fa fa-times-circle fa-2x text-danger"></i>
                    <div>Ocurrió un error al subir el archivo, por favor intentelo de nuevo!</div>
                    </span>
                </label>
            </div>
        <?php }else{ ?>
            <div class="uploader">
                <label>
                    <span style="font-size:78px">
                    <i class="fa fa-times-circle fa-2x text-danger"></i>
                    <div>El link al que está intentando acceder ha caducado!</div>
                    </span>
                </label>
            </div>
        <?php } ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script id="rendered-js">
      
            function ekUpload() {
      
                function Init() {

                    var fileSelect = document.getElementById('file-upload'),
                    fileDrag = document.getElementById('file-drag'),
                    submitButton = document.getElementById('submit-button');

                    fileSelect.addEventListener('change', fileSelectHandler, false);

                    // Is XHR2 available?
                    var xhr = new XMLHttpRequest();
                    if (xhr.upload) {
                        // File Drop
                        fileDrag.addEventListener('dragover', fileDragHover, false);
                        fileDrag.addEventListener('dragleave', fileDragHover, false);
                        fileDrag.addEventListener('drop', fileSelectHandler, false);
                    }
                }

                function fileDragHover(e) {
                    var fileDrag = document.getElementById('file-drag');
                    e.stopPropagation();
                    e.preventDefault();
                    fileDrag.className = e.type === 'dragover' ? 'hover' : 'modal-body file-upload';
                }

                function fileSelectHandler(e) {
                    // Fetch FileList object
                    var files = e.target.files || e.dataTransfer.files;
                    // Cancel event and hover styling
                    fileDragHover(e);
                    // Process all File objects
                    for (var i = 0, f; f = files[i]; i++) {
                        // if (window.CP.shouldStopExecution(0)) break;
                        parseFile(f);
                        uploadFile(f);
                    }
                    // window.CP.exitedLoop(0);
                }

                // Output
                function output(msg) {
                    // Response
                    var m = document.getElementById('messages');
                    m.innerHTML = msg;
                }

                function parseFile(file) {
                    output(
                    '<strong>' + encodeURI(file.name) + '</strong>');
                    // var fileType = file.type;
                    // console.log(fileType);
                    var imageName = file.name;
                    var isGood = /\.(?=gif|jpg|png|jpeg)/gi.test(imageName);
                    if (isGood) {
                        document.getElementById('start').classList.add("hidden");
                        document.getElementById('response').classList.remove("hidden");
                        document.getElementById('notimage').classList.add("hidden");
                        // Thumbnail Preview
                        document.getElementById('file-image').classList.remove("hidden");
                        document.getElementById('file-image').src = URL.createObjectURL(file);
                    }else {
                        document.getElementById('file-image').classList.add("hidden");
                        document.getElementById('notimage').classList.remove("hidden");
                        document.getElementById('start').classList.remove("hidden");
                        document.getElementById('response').classList.add("hidden");
                        document.getElementById("file-upload-form").reset();
                    }
                }

                function setProgressMaxValue(e) {
                    var pBar = document.getElementById('file-progress');
                    if (e.lengthComputable) {
                        pBar.max = e.total;
                    }
                }

                function updateFileProgress(e) {
                    var pBar = document.getElementById('file-progress');
                    if (e.lengthComputable) {
                        pBar.value = e.loaded;
                    }
                }

                function uploadFile(file) {

                    var xhr = new XMLHttpRequest(),
                    fileInput = document.getElementById('class-roster-file'),
                    pBar = document.getElementById('file-progress'),
                    fileSizeLimit = 1024; // In MB
                    if (xhr.upload) {
                        // Check if file is less than x MB
                        if (file.size <= fileSizeLimit * 1024 * 1024) {
                            // Progress bar
                            pBar.style.display = 'inline';
                            xhr.upload.addEventListener('loadstart', setProgressMaxValue, false);
                            xhr.upload.addEventListener('progress', updateFileProgress, false);

                            // File received / failed
                            xhr.onreadystatechange = function (e) {
                                if (xhr.readyState == 4) {
                                    // Everything is good!
                                    progress.className = (xhr.status == 200 ? "success" : "failure");
                                }
                            };
                            var formData = new FormData();
                            formData.append('file', file);
                            formData.append('sent', true);
                        } else {
                            output('Por favor selecciona un archivo menos pesado  (< ' + fileSizeLimit + ' MB).');
                        }
                    }
                }

                function sendFile(formData) {
                    // Start upload
                    xhr.open('POST', document.getElementById('file-upload-form').action, true);
                    xhr.send(formData);
                }

                // Check for the various File API support.
                if (window.File && window.FileList && window.FileReader) {
                    Init();
                } else {
                    document.getElementById('file-drag').style.display = 'none';
                }


            }
            ekUpload();
        </script>
    </body>
</html>