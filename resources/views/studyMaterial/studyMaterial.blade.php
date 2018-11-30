@extends('layouts.master')
@section('header-title')
  <title>Vchip-edu – Be partner with Vchip Technology</title>
@stop
@section('header-css')
	@include('layouts.home-css')
<link id="cpswitch" href="{{ asset('css/hover.css?ver=1.0')}}" rel="stylesheet" />
<link href="{{ asset('css/v_career.css?ver=1.0')}}" rel="stylesheet"/>
<style type="text/css">
  .tile
{
  width:100%;
  height:200px;
  margin:10px;
  background-color:#fff;
  display:inline-block;
  background-size:cover;
  position:relative;
  cursor:pointer;
  transition: all 0.4s ease-out;
  box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.44);
  overflow:hidden;
  color:white;
  font-family:'Roboto';

}
.tile img
{
  height:100%;
  width:100%;
  position:absolute;
  top:0;
  left:0;
  z-index:0;
  transition: all 0.4s ease-out;
}
.tile .text
{
   z-index:99;
  position:absolute;
  /*padding:30px;*/
  height:calc(100% - 60px);
}
.tile h1
{
  font-weight:300;
  margin:0;
  text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
  color:#e91e63;
  background-color: rgba(255,255,255,.35);
  padding:0px;
  font-size: 15px;
  top: 0px !important;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);
}
.tile h2
{
  font-weight:100;
  margin:20px 0 0 0;
  font-style:italic;
   transform: translateX(200px);
}
.tile p
{
  font-weight:300;
  margin:20px 0 0 0;
  line-height: 25px;
/*   opacity:0; */
  transform: translateX(-200px);
  transition-delay: 0.2s;
}
.animate-text
{
  padding: 50px 0px 50px 45px;
  opacity:0;
  transition: all 0.6s ease-in-out;
}
.animate-text>a
{
font-weight: bolder;
   box-shadow: 5px 5px 25px 0 rgba(46,61,73,.2);

}
.tile:hover
{
/*   background-color:#99aeff; */
box-shadow: 0px 35px 77px -17px rgba(0,0,0,0.64);
  transform:scale(1.05);
}
.tile:hover img
{
  opacity: 0.2;
}
.tile:hover .animate-text
{
  transform:translateX(0);
  opacity:1;
}
@media (min-width: 500px) and (max-width: 764px){
.tile {
     width: 60%;
     margin-left: 20%;
  }
}
@media screen and (max-width: 388px){
   .tile {
     width: 100%;
  }
}
</style>
@stop
@section('header-js')
	@include('layouts.home-js')
@stop

@section('content')
@include('header.header_menu')
<section id="vchip-background" class="mrgn_60_btm">
	<div class="vchip-background-single">
		<div class="vchip-background-img">
			<figure>
				<img src="{{ asset('images/partner.jpg')}}" class="header_img_top_pad" style="vertical-align:top; background-attachment:fixed" alt="Vchip Partners" />
			</figure>
		</div>
		<div class="vchip-background-content">
      <h2 class="animated bounceInLeft">Digital Education</h2>
    </div>
  </div>
</section>
<section id="v_section" class=" v_container v_bg_grey">
  <div class="container">
    <div class="row">
      <div class="col-md-12 mrgn_30_top">
        @if(count($categories) > 0)
          @foreach($categories as $categoryId => $category)
            <button class="accordion active" title="ANGEL INVESTOR">{{$category}}</button>
            <div class="panel_body" style="max-height: 100%">
              <div class="panel-content ">
                <ul>
                  @if(count($subcategories[$categoryId] > 0))
                    @foreach($subcategories[$categoryId] as $subcategoryId => $subcategoryArr)
                      <li><a href="{{url('study-material')}}/{{$subcategoryId}}/{{$subcategoryArr['subject']}}/{{$subcategoryArr['topic_id']}}"> {{$subcategoryArr['name']}}</a></li>
                    @endforeach
                  @endif
                 </ul>
               </div>
            </div>
          @endforeach
        @else
          No Data
        @endif
      </div>
    </div>
  </div>
</section>
@stop
@section('footer')
	@include('footer.footer')
	<script>
		var acc = document.getElementsByClassName("accordion");
		var i;

		for (i = 0; i < acc.length; i++) {
			acc[i].onclick = function() {
				this.classList.toggle("active");
				var panel = this.nextElementSibling;
				if (panel.style.maxHeight){
					panel.style.maxHeight = null;
				} else {
					panel.style.maxHeight = panel.scrollHeight + "px";
				}
			}
		}
	</script>
@stop