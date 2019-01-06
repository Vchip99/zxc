@extends('admin.master')
@section('module_title')
  <link href="{{ asset('css/dashboard.css?ver=1.0')}}" rel="stylesheet"/>
  <section class="content-header">
    <h1> Manage Clients </h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-bookmark-o"></i> Client Info </li>
      <li class="active"> Manage Clients </li>
    </ol>
  </section>
@stop
@section('admin_content')
  <div class="content-wrapper v-container tab-content" >
    <div id="student-rcd" class="">
      <div class="container">
        <div class="row">
          @if(Session::has('message'))
            <div class="alert alert-success" id="message">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ Session::get('message') }}
            </div>
          @endif
          <div class="col-lg-12" id="all-result">
            <div class="panel panel-info">
              <div class="panel-heading text-center">
                Clients Info
              </div>
              <div class="panel-body" style="min-height: 700px !important; max-height: 900px !important; ">
                <table>
                  <thead>
                    <tr>
                      <th>Sr. No.</th>
                      <th>Name of Institute</th>
                      <th>Url</th>
                      <th>Approval</th>
                      <th>Delete</th>
                    </tr>
                  </thead>
                  <tbody id="clients">
                    @if(count($clients) > 0)
                      @foreach($clients as $index => $client)
                        <tr style="overflow: auto;">
                          <td>{{$index + 1}}</td>
                          <td>{{$client->name}}</td>
                          <td>{{$client->subdomain}}</td>
                          <td>
                            @if(1 == $client->admin_approve)
                              <input type="checkbox" value="" data-client_id="{{$client->id}}" data-permission_type="admin_approve" onclick="changePermissionStatus(this);" checked="checked">
                            @else
                              <input type="checkbox" value="" data-client_id="{{$client->id}}" data-permission_type="admin_approve" onclick="changePermissionStatus(this);">
                            @endif
                          </td>
                          <td><button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_id="{{$client->id}}" onclick="deleteClient(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button>
                          <form id="deleteClient_{{$client->id}}" action="{{url('admin/deleteClient')}}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="client_id" value="{{$client->id}}">
                          </form>
                          </td>
                        </tr>
                      @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript">

  function changePermissionStatus(ele){
    var client_id = parseInt($(ele).data('client_id'));
    var permission_type = $(ele).data('permission_type');
    var message = 'Are you sure. you want to change approval of this client?';
    $.confirm({
      title: 'Confirmation',
      content: message,
      type: 'red',
      typeAnimated: true,
      buttons: {
            Ok: {
                text: 'Ok',
                btnClass: 'btn-red',
                action: function(){
                  $.ajax({
                      method: "POST",
                      url: "{{url('admin/changeClientPermissionStatus')}}",
                      data: {client_id:client_id,permission_type:permission_type}
                  })
                  .done(function( msg ) {
                    body = document.getElementById('clients');
                    body.innerHTML = '';
                    if( 0 < msg.length){
                      $.each(msg, function(idx, obj) {
                          var eleTr = document.createElement('tr');
                          var eleIndex = document.createElement('td');
                          eleIndex.innerHTML = idx + 1;
                          eleTr.appendChild(eleIndex);

                          var eleName = document.createElement('td');
                          eleName.innerHTML = obj.name;
                          eleTr.appendChild(eleName);

                          var eleUrl = document.createElement('td');
                          eleUrl.innerHTML = obj.subdomain;
                          eleTr.appendChild(eleUrl);

                          var eleAdminApproval = document.createElement('td');
                          var adminApprovalInnerHTML = '';
                          adminApprovalInnerHTML = '<input type="checkbox" value="" data-client_id="'+obj.id+'" data-permission_type="admin_approve" onclick="changePermissionStatus(this);"';
                          if( 1 == obj.admin_approve){
                            adminApprovalInnerHTML += 'checked = checked';
                          }
                          adminApprovalInnerHTML += '>';
                          eleAdminApproval.innerHTML = adminApprovalInnerHTML;
                          eleTr.appendChild(eleAdminApproval);

                          var eleDelete = document.createElement('td');
                          var deleteUrl = "{{url('admin/deleteClient')}}";
                          var csrfField = '{{ csrf_field() }}';
                          var deleteMethod = '{{ method_field('DELETE') }}';
                          deleteInnerHTML = '';
                          deleteInnerHTML +='<button class="btn btn-danger btn-xs delet-bt delet-btn" data-title="Delete" data-toggle="modal" data-target="#delete" data-client_id="'+obj.id+'" onclick="deleteClient(this);" ><span class="fa fa-trash-o" data-placement="top" data-toggle="tooltip" title="Delete"></span></button><form id="deleteClient_'+obj.id+'" action="'+deleteUrl+'" method="POST" style="display: none;">'+csrfField+''+deleteMethod+'<input type="hidden" name="client_id" value="'+obj.id+'"></form>';
                          eleDelete.innerHTML = deleteInnerHTML;
                          eleTr.appendChild(eleDelete);

                          body.appendChild(eleTr);
                      });
                    }
                  });
                }
            },
            Cancel: function () {
              if('checked' == $(ele).attr('checked')){
                $(ele).prop('checked', 'checked');
              } else {
                $(ele).prop('checked', '');
              }
            }
        }
    });


  }

  function deleteClient(ele){;
    var clientId = $(ele).data('client_id');
    if(clientId > 0){
      $.confirm({
        title: 'Confirmation',
        content: 'If You delete this client, client will be permanently deleted.',
        type: 'red',
        typeAnimated: true,
        buttons: {
              Ok: {
                  text: 'Ok',
                  btnClass: 'btn-red',
                  action: function(){
                    document.getElementById('deleteClient_'+clientId).submit();
                  }
              },
              Cancel: function () {
              }
          }
        });
    }
  }
</script>
@stop