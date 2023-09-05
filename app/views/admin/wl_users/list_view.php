<div class="panel panel-inverse">
    <!-- BEGIN panel-heading -->
    <div class="panel-heading">
        <h4 class="panel-title">Users</h4>
        <div class="panel-heading-btn">
            <a href="#userAddModal" data-bs-toggle="modal" class="btn btn-warning btn-xs"><i class="fa fa-plus"></i> Add</a>
            <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
        </div>
    </div>
    <!-- END panel-heading -->

    <!-- BEGIN panel-body -->
    <div class="panel-body">
        <div class="row mb-15px">
            <div class="form-floating col-3">
                <select class="form-select" title="Select type" id="selectUserType">
                    <option value="0">All users</option>
                    <?php $wl_user_types = $this->db->getAllDataByFieldInArray('wl_user_types', ['active' => 1], 'id DESC');
                    foreach ($wl_user_types as $type) {
                        echo "<option value=\"{$type->id}\">{$type->title}</option>";
                    } ?>
                </select>
                <label for="selectUserType" class="d-flex align-items-center text-gray-600 fs-13px">User type</label>
            </div>
            <div class="form-floating col-3">
                <select class="form-select" title="Select type" id="selectUserStatus">
                    <option value="0">All users</option>
                    <?php $wl_user_status = $this->db->getAllData('wl_user_status');
                    foreach ($wl_user_status as $status) {
                        echo "<option value=\"{$status->id}\">{$status->title}</option>";
                    } ?>
                </select>
                <label for="selectUserType" class="d-flex align-items-center text-gray-600 fs-13px">User status</label>
            </div>
        </div>

        <table id="users-table" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-nowrap">ID</th>
                    <th width="1%">&nbsp;</th>
                    <th class="text-nowrap">Name</th>
                    <th class="text-nowrap">Email</th>
                    <th class="text-nowrap">Phone</th>
                    <th class="text-nowrap">Type</th>
                    <th class="text-nowrap">Status</th>
                    <th class="text-nowrap">Created at</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <!-- END panel-body -->
</div>
<!-- END panel -->

<div class="modal fade" id="userDetalModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">User <strong></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="spinner"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userAddModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add <strong>User</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= SITE_URL ?>admin/wl_users/add" method="POST" id="userAddForm" class="ajax" data-after="afterUserAdd">
                    <?php $type = ['email' => 'email'];
                    foreach (['email' => 'E-mail', 'name' => 'Name', 'phone' => 'Phone'] as $key => $title) { ?>
                        <div class="form-floating mb-15px">
                            <input type="<?= $type[$key] ?? 'text' ?>" class="form-control fs-13px h-45px" name="<?= $key ?>" id="<?= $key ?>" placeholder="<?= $title ?>" required />
                            <label for="<?= $key ?>" class="d-flex align-items-center text-gray-600 fs-13px"><?= $title ?></label>
                        </div>
                    <?php }
                    if ($this->user->type_id == 1) { ?>
                        <div class="form-floating mb-20px">
                            <select name="type_id" class="form-select" id="selectType">
                                <?php foreach ($wl_user_types as $type) {
                                    echo "<option value=\"{$type->id}\">{$type->title}</option>";
                                } ?>
                            </select>
                            <label for="selectType" class="d-flex align-items-center text-gray-600 fs-13px">User type (Access mode)</label>
                        </div>
                    <?php } ?>
                    <div class="form-floating mb-20px">
                        <select name="selectPassword" class="form-select" id="selectPassword">
                            <option value="by_email">Send by e-mail</option>
                            <option value="set">Set in thid form</option>
                        </select>
                        <label for="selectPassword" class="d-flex align-items-center text-gray-600 fs-13px">User password</label>
                    </div>
                    <div class="form-floating hide mb-15px" id="setPassword">
                        <input type="text" class="form-control fs-13px h-45px" name="password" id="password" placeholder="Set user password" />
                        <label for="password" class="d-flex align-items-center text-gray-600 fs-13px">Set user password</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="userAddForm" class="btn btn-warning"><i class="fas fa-plus"></i> Add</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- ================== BEGIN page-js ================== -->
<?php $this->page->css = 'assets/plugins/datatables/datatables.min.css'; ?>
<?php $this->page->js_load = 'assets/plugins/datatables/datatables.min.js'; ?>
<?php $this->page->js_init = 'init__dataTable()'; ?>
<script>
    function init__dataTable() {
        window.usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            lengthMenu: [25, 50, 100],
            ajax: {
                url: '<?= SERVER_URL ?>admin/wl_users/data',
                data: {
                    type_id: function() {
                        return $('#selectUserType').val();
                    },
                    status_id: function() {
                        return $('#selectUserStatus').val();
                    }
                }
            },
            columns: [{
                    data: 'id'
                }, {
                    data: 'eye',
                    "orderable": false
                }, {
                    data: 'name'
                }, {
                    data: 'email'
                }, {
                    data: 'phone'
                }, {
                    data: 'leader_name'
                }, {
                    data: 'type_title'
                }, {
                    data: 'status_title'
                }, {
                    data: 'created_at'
                }
            ],
            order: [
                [1, "desc"]
            ]
        });

        $('#selectUserType').change(function() {
            window.usersTable.ajax.reload();
        });

        $('#selectUserStatus').change(function() {
            window.usersTable.ajax.reload();
        });

        $('#userDetalModal').on('show.bs.modal', function(event) {
            // Button that triggered the modal
            var btn = event.relatedTarget,
                id = $(btn).data('id'),
                href = $(btn).attr('href');

            $('#userDetalModal').find('.modal-title strong').text('#' + id);
            $('#userDetalModal').find('.modal-body').html('<div class="spinner"></div>');
            $.ajax({
                url: href,
                type: 'POST',
                success: function(res) {
                    $('#userDetalModal .modal-body').html(res);
                },
                error: function() {
                    swal({
                        title: "Error!",
                        text: "Try Again!",
                        icon: "error"
                    });
                },
                timeout: function() {
                    swal({
                        title: "Timeout Error!",
                        text: "Try Again!",
                        icon: "error"
                    });
                }
            });
        });

        $('#selectPassword').change(function(event) {
            if ($(this).val() == 'set') {
                $('#setPassword').removeClass('hide');
                $('#password').attr('required', true);
            } else {
                $('#setPassword').addClass('hide');
                $('#password').attr('required', false);
            }
        });
    }

    function afterUserAdd(res) {
        window.usersTable.ajax.reload();
        $('#userAddModal').modal('hide');
    }
</script>