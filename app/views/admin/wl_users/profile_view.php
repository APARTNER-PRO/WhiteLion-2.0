<!-- begin row -->
<div class="row">
    <!-- begin col-6 -->
    <div class="col-md-6">
        <!-- begin panel -->
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">About user</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <td>Email</td>
                            <td><?=$this->user->email?></td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td><?=$this->user->name?></td>
                        </tr>
                        <tr>
                            <td>User type</td>
                            <td><?=$this->user->type_title?></td>
                        </tr>
                        <tr>
                            <td>Registered</td>
                            <td><?=date("d.m.Y H:i", $this->user->registered)?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end panel -->
    </div>
    <!-- end col-6 -->
    <div class="col-md-6">
        <!-- begin panel -->
        <div class="panel panel-inverse" data-sortable-id="form-stuff-2">
            <div class="panel-heading">
                <h4 class="panel-title">Change password</h4>
            </div>
            <div class="panel-body">
                <form action="<?=SITE_URL?>admin/wl_users/changePassword" method="POST" class="ajax" data-before="comparePasswords">
                    <div class="form-floating mb-15px">
                        <input type="password" name="password" required class="form-control fs-15px" id="CurrentPassword" placeholder="Current password" />
                        <label for="CurrentPassword" class="d-flex align-items-center fs-13px">
                            Current password
                        </label>
                    </div>

                    <div class="form-floating mb-15px">
                        <input type="password" name="new-password" required class="form-control fs-15px" id="newPassword" placeholder="New password" />
                        <label for="newPassword" class="d-flex align-items-center fs-13px">
                            New password
                        </label>
                    </div>

                    <div class="form-floating mb-15px">
                        <input type="password" name="re-new-password" required class="form-control fs-15px" id="reNewPassword" placeholder="Re New password" />
                        <label for="reNewPassword" class="d-flex align-items-center fs-13px">
                            Re new password
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-5px"></i> Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
	<!-- begin col-6 -->
    <div class="col-md-12">
        <!-- begin panel -->
        <div class="panel panel-inverse" data-sortable-id="form-stuff-3">
            <div class="panel-heading">
                <h4 class="panel-title">Register</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered">
                <thead>
					<tr>
						<th>id</th>
						<th>Date time</th>
						<th>Action</th>
						<th>Additional</th>
					</tr>
				</thead>
				<tbody>
				<?php if( $register = $this->db->select('wl_user_register as r', '*', ['user_id' => $this->user->id, '#a.public' => 1])
                                ->join('wl_user_register_actions as a', 'title_public, help_additionall', '#r.action_id')
                                ->order('id DESC')
                                ->limit(50)
                                ->get('array') )
                foreach ($register as $r) { ?>
					<tr>
						<td><?=$r->id?></td>
						<td><?=date("d.m.Y H:i", $r->action_at)?></td>
						<td><?=$r->title_public?></td>
						<td title="<?=$r->help_additionall?>"><?=$r->additionally?></td>			
					</tr>
				<?php } ?>
				</tbody>
			</table>
            </div>
        </div>
        <!-- end panel -->
    </div>
    <!-- end col-6 -->
    
</div>
<!-- end row -->

<script>
    function comparePasswords() {
        if(newPassword.value != reNewPassword.value)
        {
            swal("New password and Re new password do not match!", { icon: "error" });
            return false;
        }
    }
</script>