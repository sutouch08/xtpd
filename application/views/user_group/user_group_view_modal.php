<!--  Add New Address Modal  --------->
<div class="modal fade" id="UserGroupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:solid 1px #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title-site" style="margin-bottom:0px;" >User Group Detail</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <table class="table table-bordered border-1" style="margin-bottom:0px;">
                    <tbody id="result">

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary" onClick="dismiss('UserGroupModal')" >Close</button>
            </div>
        </div>
    </div>
</div>

<script id="detail-template" type="text/x-handlebarsTemplate">
  <tr><td class="width-40 text-right">#id</td><td colspan="4">{{id}}</td></tr>
  <tr><td class="width-40 text-right">Group Name</td><td colspan="4">{{name}}</td></tr>
  <tr><td class="width-40 text-right">Member</td><td colspan="4">{{member}}</td></tr>
  <tr><td class="width-40 text-right">Create By</td><td colspan="4">{{add_by}}</td></tr>
  <tr><td class="width-40 text-right">Create Date</td><td colspan="4">{{date_add}}</td></tr>
  <tr><td class="width-40 text-right">Update By</td><td colspan="4">{{update_by}}</td></tr>
  <tr><td class="width-40 text-right">Last Update</td><td colspan="4">{{date_upd}}</td></tr>
  <tr class="font-size-14" style="background-color:#428bca73;"><td class="middle">Permission</td>
  <td class="middle text-center">View</td>
  <td class="middle text-center">Add</td>
  <td class="middle text-center">Edit</td>
  <td class="middle text-center">Delete</td>
  </tr>
  {{#each permission}}
    {{#if menu}}
      {{#each menu}}
        <tr>
        <td class="middle" style="padding-left:20px;">{{menu_name}} </td>
        <td class="middle text-center">{{#if can_view}}<i class="fa fa-check green"></i>{{else}}<i class="fa fa-times red"></i>{{/if}}</td>
        <td class="middle text-center">{{#if can_add}}<i class="fa fa-check green"></i>{{else}}<i class="fa fa-times red"></i>{{/if}}</td>
        <td class="middle text-center">{{#if can_edit}}<i class="fa fa-check green"></i>{{else}}<i class="fa fa-times red"></i>{{/if}}</td>
        <td class="middle text-center">{{#if can_delete}}<i class="fa fa-check green"></i>{{else}}<i class="fa fa-times red"></i>{{/if}}</td>
        </tr>
      {{/each}}
    {{/if}}
  {{/each}}
</script>
