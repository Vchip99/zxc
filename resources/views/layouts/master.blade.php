<!DOCTYPE html>
<html lang="en"/>

<head>
  <link rel="SHORTCUT ICON" href="{{ asset('images/logo/vedu.png') }}"/>
  @yield('header-title')
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  <meta name="description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />
  <meta name="author" content=" Vchip Technology" />
  <meta name="keywords" content="V-edu, vchipedu, Education sector, Online Courses, Digital Education, eLearning, Online learning, Online test series, Webinars, Online live courses, Live discussion,  vchip, Technology, vchip Technology, vchip Technology private ltd, vchip design and training, vchip design pvt ltd, vchip design and training pvt ltd, vishesh agrawal, web development, IoT, Internet of things, M2M, Mobile app development, Android app development, cloud formation, Internet of Everything, health sector, agriculture sector, food sector, Pune, Amravati." />

 <!-- Schema.org markup for Google+ -->
  <meta itemprop="name" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
  <meta itemprop="description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />
  <meta itemprop="image" content="img/V-edu_social_share.jpg" />

 <!-- Twitter card -->
 <meta name="twitter:card" content="summary_large_image" />
 <meta name="twitter:title" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
 <meta name="twitter:site" content="@V-edu" />
 <meta name="twitter:creator" content="@V-edu"/>
 <meta name="twitter:image" content="img/V-edu_social_share.jpg" />
 <meta name="twitter:description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations." />

 <!-- Open graph  -->
 <meta property="og:type"   content="website" />
 <meta property="og:url"    content="http://vchipedu.com/" />
 <meta property="og:site_name" content="vchipedu.com" />
 <meta property="og:title" content="V-edu - Digital Education, Online Courses & eLearning |Vchip Technology" />
 <meta property="og:image"  content="img/V-edu_social_share.jpg" />
 <meta name="og:description" content="Vchip Technology is working on Digital Education platform with name V-edu, V-edu provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. We are currently focusing on formation of Digital Villages and bridging between well establish Industry/start-ups and educational organizations."/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  @yield('header-css')
  @yield('header-js')
</head>
<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">
  @yield('content')
  @yield('footer')
<script type="text/javascript">
  $(document).ready(function(){
    setTimeout(function() {
      $('.alert-success').fadeOut('fast');
    }, 50000);
  });

</script>
</body>
</html>
