<div class="container mb-3" style="padding-top: 88px;" id="container">
    <h1 class="display-4 font-weight-bold text-center text-coekku">Problem</h1>
    <?php if (isLogin() && isAdmin($_SESSION['id'], $conn)) { ?><a href="../problem/create" class="btn btn-coekku btn-sm">+ Add Problem</a><?php } ?>
    <div class="table-responsive">
        <table class="table table-hover w-100 d-block d-md-table" id="problemTable">
            <thead>
                <tr class="text-nowrap">
                    <th scope="col" class="font-weight-bold text-coekku text-right">ID</th>
                    <th scope="col" class="font-weight-bold text-coekku">Task</th>
                    <th scope="col" class="font-weight-bold text-coekku">Rate</th>
                    <th scope="col" class="font-weight-bold text-coekku">Result</th>
                </tr>
            </thead>
            <tbody class="text-nowrap">
                <?php
                $html = "";
                if ($stmt = $conn -> prepare("SELECT id,name,properties,codename FROM `problem` ORDER BY id")) {
                    //$stmt->bind_param('ii', $page, $limit);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id']; $name = $row['name']; $codename = $row['codename'];
                            
                            $prop = json_decode($row['properties'],true);
                            $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                            $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;

                            if (!$hide || (isLogin() && isAdmin($_SESSION['id'], $conn))) {
                                $lastResult = isLogin() ? lastResult($_SESSION['id'], $id, $conn) : "";
                                $html .= "<tr onclick='window.open(\"../problem/$id\")'>
                                    <th class='text-right' scope='row'><a href=\"../problem/$id\" target=\"_blank\">$id</a></th>
                                    <td><a href=\"../problem/$id\" target=\"_blank\">$name <span class='badge badge-coekku'>$codename</span></a></td>
                                    <td data-order='".$rate."'>".rating($rate)."</td>
                                    <td><code>$lastResult</code></td>
                                </tr>";
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();  
                    }
                    echo $html;
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function () {
            $('#problemTable').DataTable({
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"] ],
                'columnDefs': [ {
                    'targets': [1,3], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns
                }]
            });
            $('.dataTables_length').addClass('bs-select');
        });
    </script>
</div>