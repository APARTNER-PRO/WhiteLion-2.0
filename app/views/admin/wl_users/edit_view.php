<div class="form-floating mb-15px">
    <input type="text" class="form-control fs-13px h-45px" value="<?= $user->id ?>" id="userEditId" readonly />
    <label for="userEditId" class="d-flex align-items-center text-gray-600 fs-13px">ID</label>
</div>

<?php $type = ['email' => 'email'];
foreach (['email' => 'E-mail', 'name' => 'Name', 'phone' => 'Phone'] as $key => $title) { ?>
    <div class="form-floating mb-15px">
        <input type="<?= $type[$key] ?? 'text' ?>" class="form-control fs-13px h-45px on-editUserField" name="<?= $key ?>" id="<?= $key ?>" placeholder="<?= $title ?>" value="<?= $user->$key ?>" <?= $this->user->type_id == 1 ? '' : 'disabled' ?> />
        <label for="<?= $key ?>" class="d-flex align-items-center text-gray-600 fs-13px"><?= $title ?></label>
    </div>
<?php }

if ($this->user->type_id == 1) { ?>
    <div class="form-floating mb-20px">
        <select name="type_id" class="form-select on-editUserField" id="selectType">
            <?php foreach ($this->db->getAllDataByFieldInArray('wl_user_types', ['active' => 1], 'id DESC') as $type) {
                $selected = $user->type_id == $type->id ? 'selected' : '';
                echo "<option value=\"{$type->id}\" {$selected}>{$type->title}</option>";
            } ?>
        </select>
        <label for="selectType" class="d-flex align-items-center text-gray-600 fs-13px">User type (Access mode)</label>
    </div>
<?php }
if ($user->type_id > 2) { ?>
    <div class="form-floating mb-20px">
        <select name="leader_id" class="form-select on-editUserField" id="leader" <?= $this->user->type_id == 1 ? '' : 'disabled' ?>>
            <option value="0">None</option>
            <?php $leader_type = $user->type_id - 1;
            foreach ($this->db->getAllDataByFieldInArray('wl_users', ['type_id' => $leader_type], 'id DESC') as $leader) {
                $selected = $user->leader_id == $leader->id ? 'selected' : '';
                echo "<option value=\"{$leader->id}\" {$selected}>#{$leader->id} {$leader->name}</option>";
            } ?>
        </select>
        <label for="leader" class="d-flex align-items-center text-gray-600 fs-13px">Leader</label>
    </div>
<?php } ?>

<p>Registered: <strong><?= date('d.m.Y H:i', $user->registered) ?></strong></p>
<p>Last login: <strong><?= date('d.m.Y H:i', $user->last_login) ?></strong></p>

<?php if ($this->user->type_id == 1) { ?>
    <button type="button" class="btn btn-danger" onclick="userDelete()"><i class="far fa-trash-alt"></i> Delete</button>
    <button type="button" class="btn btn-info" onclick="newPassword()"><i class="fas fa-key"></i> Set password</button>
<?php } ?>

<script>
    $('.on-editUserField').change(function() {
        let user_id = userEditId.value,
            type = $(this).attr('type'),
            field = $(this).attr('name'),
            value = $(this).val(),
            title = $(this).parent().find('label').text(),
            gritter_text = '';

        if (type == 'checkbox')
            value = $(this).is(':checked');

        wl.ajax('wl_users/edit', {
                user_id: user_id,
                field: field,
                value: value
            }, false)
            .then((res) => {
                if (res.status == 'success') {
                    if (field == 'name') {
                        $('#row_' + user_id).find('td:eq(2)').html(value);
                    }
                    if (field == 'email') {
                        $('#row_' + user_id).find('td:eq(3)').html(value);
                    }
                    if (field == 'phone') {
                        $('#row_' + user_id).find('td:eq(4)').html(value);
                    }
                    if (field == 'leader_id') {
                        value = $('#leader option:selected').text();
                        $('#row_' + user_id).find('td:eq(5)').html(value);
                    }
                    if (field == 'type_id') {
                        value = $('#selectType option:selected').text();
                        $('#row_' + user_id).find('td:eq(6)').html(value);
                    }

                    $.gritter.add({
                        title: 'User #' + user_id + ' updated successfull',
                        text: 'Update ' + title + ': <strong>' + value + '</strong>',
                        sticky: true,
                        class_name: 'my-sticky-class gritter-light'
                    });
                }
            });
    });

    function userDelete() {
        let user_id = userEditId.value,
            name = $('input.on-editUserField[name=name]').val();
        swal({
            title: 'Delete ' + name + '?',
            text: 'User Id #' + user_id + '. *The action cannot be undone',
            content: {
                element: "input",
                attributes: {
                    placeholder: "Type your password",
                    type: "password",
                    required: true
                }
            },
            icon: 'warning',
            dangerMode: true,
            buttons: true
        }).then((password) => {
            if (password) {
                wl.ajax('wl_users/delete', {
                        user_id: user_id,
                        password: password
                    })
                    .then((res) => {
                        if (res.status == 'success') {
                            $('#row_' + user_id).hide();
                            $('#userDetalModal').modal('hide');
                        }
                    });
            }
        });
    };

    function newPassword() {
        let user_id = userEditId.value,
            name = $('input.on-editUserField[name=name]').val();
        swal({
            title: 'Set password to ' + name + '?',
            content: {
                element: "input",
                attributes: {
                    placeholder: "Type new user password",
                    type: "text",
                    required: true
                }
            },
            icon: 'warning',
            dangerMode: true,
            buttons: true
        }).then((password) => {
            if (password) {
                wl.ajax('wl_users/changePassword', {
                    user_id: user_id,
                    password: password
                });
            }
        });
    };
</script>