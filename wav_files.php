<?php include('header.php'); ?>
<section id="page-content">
    <div class="body-content animated fadeIn">
        <div class="row">
            <div class="col-md-12">
                <div class="panel rounded shadow">
                    <div class="panel-heading">
                        <div class="pull-left">
                            <h3 class="panel-title" style="color: #fff">Wav Files List</h3>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <table id="datatable-responsives" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File_name</th>
                                    <th>Date Time</th>
                                </tr>
                            </thead>
                            <?php
                            date_default_timezone_set('Asia/Kolkata');
                            $server_path = 'http://172.104.177.75/call_analyse/wav/voice_uploads/';
                            $files = glob("../wav/voice_uploads/*.mp3");
                            $count = 1;
                            foreach ($files as $key => $fileName) {
                                $fileName = str_replace('../wav/voice_uploads/', '', $fileName);
                                $explode = explode('_',$fileName);
                                $unix = @substr($explode[2],0,10);
                                echo "<tr>";
                                    echo "<td>".$count."</td>";
                                    echo "<td><a target='__blank' href=".$server_path.$fileName.">".$fileName."</a></td>";
                                    echo "<td>".date("d-m-Y h:i:s A", $unix)."</td>";
                                echo "</tr>";
                                $count++;
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <link href="assets/admin/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <script src="assets/admin/js/jquery.dataTables.min.js"></script>
    <script src="assets/admin/js/dataTables.bootstrap.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#datatable-responsives').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false,
            "order": [[ 2, "desc" ]]
        });
    });
    </script>

    <?php include('footer.php'); ?>