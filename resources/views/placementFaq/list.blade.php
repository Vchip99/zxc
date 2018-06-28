@extends('admin.master')
@section('module_title')
  <section class="content-header">
    <h1> Manage Placement Faq</h1>
    <ol class="breadcrumb">
      <li><i class="fa fa-gift"></i> Placement </li>
      <li class="active"> Manage Placement Faq</li>
    </ol>
  </section>
@stop
@section('admin_content')
&nbsp;
  <style type="text/css">
    /*read-more*/
      .morecontent span {
          display: none;
      }
      .morelink {
          display: block;
      }
  </style>
  <div class="container">
  @if(Session::has('message'))
    <div class="alert alert-success" id="message">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        {{ Session::get('message') }}
    </div>
  @endif
  <div class="form-group row">
    <div id="addSubCategoryDiv">
      <a id="addSubCategory" href="{{url('admin/createPlacementFaq')}}" type="button" class="btn btn-primary" style="float: right;" title="Add New Placement Faq">Add New Placement Faq</a>&nbsp;&nbsp;
    </div>
  </div>
  <div>
    <table class="table admin_table">
      <thead class="thead-inverse">
        <tr>
          <th>#</th>
          <th>Company Name</th>
          <th>Area Name</th>
          <th>Question</th>
          <!-- <th>Answer</th> -->
          <th>Edit Faq</th>
          <th>Delete Company</th>
        </tr>
      </thead>
      <tbody>
        @if(count($placementFaqs)>0)
          @foreach($placementFaqs as $index => $placementFaq)
          <tr>
            <td scope="row">{{$index + $placementFaqs->firstItem()}}</td>
            <td>{{$placementFaq->area->name}}</td>
            <td>{{$placementFaq->company->name}}</td>
            <td class="more">{{$placementFaq->question}}</td>
            <!-- <td class="more">{{$placementFaq->answer}}</td> -->
            <td>
              <a href="{{url('admin/placementFaq')}}/{{$placementFaq->id}}/edit" ><img src="{{asset('images/edit1.png')}}" width='30' height='30' title="Edit {{$placementFaq->name}}" />
                </a>
            </td>
            <td>
                <a id="{{$placementFaq->id}}" onclick="confirmDelete(this);"><img src="{{asset('images/delete2.png')}}" width='30' height='30' title="Delete {{$placementFaq->name}}" />
                <form id="deleteFaq_{{$placementFaq->id}}" action="{{url('admin/deletePlacementFaq')}}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <input type="hidden" name="faq_id" value="{{$placementFaq->id}}">
                </form>
            </td>
          </tr>
          @endforeach
        @else
          <tr><td colspan="4">No placement faq is created.</td></tr>
        @endif
      </tbody>
    </table>
    <div style="float: right;">
      {{ $placementFaqs->links() }}
    </div>
  </div>
  </div>

  <script type="text/javascript">

    function confirmDelete(ele){
      $.confirm({
          title: 'Confirmation',
          content: 'Do you want to delete this faq.',
          type: 'red',
          typeAnimated: true,
          buttons: {
                Ok: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                      var id = $(ele).attr('id');
                      formId = 'deleteFaq_'+id;
                      document.getElementById(formId).submit();
                    }
                },
                Cancle: function () {
                }
            }
          });
    }

  var showChar = 60;
  var ellipsestext = "...";
  var moretext = "Read more";
  var lesstext = "less";
  $('.more').each(function() {
    var content = $(this).html();

    if(content.length > showChar) {

      var c = content.substr(0, showChar);
      var h = content.substr(showChar-1, content.length - showChar);

      var html = c + '<span class="moreellipses" style="color:#8f10b5; margin-left:5px;">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink" style="color:#8f10b5";>' + moretext + '</a></span>';

      $(this).html(html);
    }

  });

  $(".morelink").click(function(){
    if($(this).hasClass("less")) {
      $(this).removeClass("less");
      $(this).html(moretext);
    } else {
      $(this).addClass("less");
      $(this).html(lesstext);
    }
    $(this).parent().prev().toggle();
    $(this).prev().toggle();
    return false;
  });
</script>
@stop