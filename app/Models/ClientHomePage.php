<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect, DB, Auth;
use App\Libraries\InputSanitise;

class ClientHomePage extends Model
{
    protected $connection = 'mysql2';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['client_id','subdomain','home_content_value','background_image','home_about_value','home_about_content','about_show_hide','home_vission_content','home_mission_content','home_course_name','home_course_content','course_show_hide','home_test_value','home_test_content','test_show_hide', 'customer_show_hide', 'home_customer_value', 'home_customer_content', 'testimonial_show_hide', 'testimonial_value','team_show_hide', 'contact_us', 'institute_name', 'institute_url', 'facebook_url', 'twitter_url', 'google_url', 'linkedin_url'];

    protected static function updateClientHomePage(Request $request){
    	// $subdomain = $request->subdomain;

		// $subDomainHome = static::where('subdomain', $request->subdomain)->first();
        $subDomainHome = static::where('client_id', Auth::guard('client')->user()->id)->first();

        if(!empty($request->home_content_value)){
    	   $subDomainHomeArr['home_content_value'] = $request->home_content_value;
        }
        $subdomainArr = explode('.', $request->subdomain);
        $clientName = $subdomainArr[0];

        if(!empty($request->file('background_image'))){
            // $subDomainHomeArr['background_image'] = $request->background_image;

            $backgroundImage = $request->file('background_image')->getClientOriginalName();
            $backgroundImageFolder = "client_images/".$clientName;

            if(!is_dir($backgroundImageFolder)){
                mkdir($backgroundImageFolder, 0755, true);
            }
            $backgroundImagePath = $backgroundImageFolder ."/". $backgroundImage;
            if(file_exists($backgroundImagePath)){
                unlink($backgroundImagePath);
            } elseif(!empty($subDomainHome->id) && file_exists($subDomainHome->background_image)){
                // unlink($subDomainHome->background_image);
            }
            $request->file('background_image')->move($backgroundImageFolder, $backgroundImage);
            $subDomainHomeArr['background_image'] = "background-image: url('".$backgroundImagePath."');background-attachment: fixed;
                  background-position: center;
                  background-size:cover;
                  -webkit-background-size:cover;
                  -moz-background-size:cover;
                  -o-background-size:cover;";
        }
        if(!empty($request->home_about_value)){
           $subDomainHomeArr['home_about_value'] = $request->home_about_value;
        }
        if(!empty($request->home_about_content)){
           $subDomainHomeArr['home_about_content'] = $request->home_about_content;
        }
        if(!empty($request->home_vission_content)){
           $subDomainHomeArr['home_vission_content'] = $request->home_vission_content;
        }
        if(!empty($request->home_mission_content)){
           $subDomainHomeArr['home_mission_content'] = $request->home_mission_content;
        }
        $subDomainHome->about_show_hide = $request->about_section;

        if(!empty($request->home_course_name)){
           $subDomainHomeArr['home_course_name'] = $request->home_course_name;
        }
        if(!empty($request->home_course_content)){
           $subDomainHomeArr['home_course_content'] = $request->home_course_content;
        }
        $subDomainHome->course_show_hide = $request->course_section;

        if(!empty($request->home_test_value)){
           $subDomainHomeArr['home_test_value'] = $request->home_test_value;
        }
        if(!empty($request->home_test_content)){
           $subDomainHomeArr['home_test_content'] = $request->home_test_content;
        }
        $subDomainHome->test_show_hide = $request->test_section;

        $subDomainHomeArr['customer_show_hide'] = $request->customer_section;

        if(!empty($request->home_customer_value)){
           $subDomainHomeArr['home_customer_value'] = $request->home_customer_value;
        }

        if(!empty($request->home_customer_content)){
           $subDomainHomeArr['home_customer_content'] = $request->home_customer_content;
        }

        if(!empty($request->testimonial_value)){
           $subDomainHomeArr['testimonial_value'] = $request->testimonial_value;
        }

        $subDomainHomeArr['testimonial_show_hide'] = $request->testimonial_section;
        $subDomainHomeArr['team_show_hide'] = $request->team_section;

        if(!empty($request->contact_us)){
           $subDomainHomeArr['contact_us'] = $request->contact_us;
        }

        if(!empty($request->institute_name)){
           $subDomainHomeArr['institute_name'] = $request->institute_name;
        }

        if(!empty($request->institute_url)){
           $subDomainHomeArr['institute_url'] = $request->institute_url;
        }

        if(!empty($request->facebook_url)){
           $subDomainHomeArr['facebook_url'] = $request->facebook_url;
        }

        if(!empty($request->twitter_url)){
           $subDomainHomeArr['twitter_url'] = $request->twitter_url;
        }

        if(!empty($request->google_url)){
           $subDomainHomeArr['google_url'] = $request->google_url;
        }

        if(!empty($request->linkedin_url)){
           $subDomainHomeArr['linkedin_url'] = $request->linkedin_url;
        }
        $subDomainHome->update($subDomainHomeArr);

    	return $subDomainHome;
    }

    protected static function addClientHomePage($client){

        $subDomainHome = new static;
        $subDomainHome->client_id = $client->id;
        $subDomainHome->subdomain = $client->subdomain;
        $subDomainHome->home_content_value = 'Digital Education';
        $subDomainHome->background_image = '';
        $subDomainHome->home_about_value = 'ABOUT US';
        $subDomainHome->home_about_content = 'Education is need of better society and in our country(India) most of people are live in villages . So V-edu is working on Digital Education platform, so that we can provide great education platform equally in villages and remote areas along with urban area. In other word you can learn with fun from anywhere in the world. We always believes that better society is a best place to live and educated society is best society. So at initial stage will we provide our services, V-edu platform at basic pay and after 2020 we will open V-edu platform completely free of cost for society.';
        $subDomainHome->about_show_hide = 1;
        $subDomainHome->home_vission_content = 'To be at leading and respectable place in the knowledge led creativity movement. World see toward our country as leading industry of Electronics and IT sector.';
        $subDomainHome->home_mission_content = 'Education is need of better society and in our country most of people are depend on farm. So Vchip is working on Education and Agriculture sector at first. At initial stage these two sector help a company for initial money transaction. After 2020 we will open these two platform completely free of cost for society . Vchip is also work in Health and Food sector. These two sector are main source of money transaction of Vchip. We are mostly focused on our customer requirement and always work for completion of their demands with satisfaction. We are giving importance to creative minds and always be with the one having innovative ideas. We give priority to work in union and always like to work in competitive environment. We are at the learning stage and will always be at same place since we believe that there is something to learn to implement new ideas with the use of experience.';
        $subDomainHome->home_course_name = 'ONLINE COURSES';
        $subDomainHome->home_course_content = 'DIGITAL EDUCATION...';
        $subDomainHome->course_show_hide = 0;
        $subDomainHome->home_test_value = 'ONLINE TEST SERIES';
        $subDomainHome->home_test_content = 'TEST SERIES IN YOUR POCKET';
        $subDomainHome->test_show_hide = 0;

        $subDomainHome->customer_show_hide = 0;
        $subDomainHome->home_customer_value = 'OUR CUSTOMERS';
        $subDomainHome->home_customer_content = 'HAPPY CUSTOMERS...SUCCESSFUL ADVENTURE...';

        $subDomainHome->testimonial_show_hide = 0;
        $subDomainHome->team_show_hide = 0;

        $subDomainHome->contact_us = 'VCHIP TECHNOLOGY PVT LTD <br />
                                    Address: GITANJALI COLONY, NEAR RAJYOG SOCIETY,<br />
                                    WARJE, PUNE-411058, INDIA.<br />
                                    Email: info@vchiptech.com';
        $subDomainHome->institute_name = 'Vchip Design Sys Pvt Ltd.';
        $subDomainHome->institute_url = 'http://www.vchiptech.com/';
        $subDomainHome->facebook_url = 'https://www.facebook.com/';
        $subDomainHome->twitter_url = 'https://twitter.com/';
        $subDomainHome->google_url = 'https://plus.google.com';
        $subDomainHome->linkedin_url = 'https://in.linkedin.com/';

        $subDomainHome->save();

        return $subDomainHome;
    }

}
