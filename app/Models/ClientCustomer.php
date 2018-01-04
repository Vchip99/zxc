<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\ClientAllComment;
use App\Libraries\InputSanitise;
use Auth;
use Intervention\Image\ImageManagerStatic as Image;

class ClientCustomer extends Model
{
    protected $connection = 'mysql2';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'image', 'url', 'client_id'];

    protected static function updateCustomer(Request $request){
        $ArrVal = array_keys($request->all());
        $clientCustomers = static::where('client_id', Auth::guard('client')->user()->id)->get();
        $subdomainArr = explode('.', Auth::guard('client')->user()->subdomain);
        $clientName = $subdomainArr[0];

        if(count($clientCustomers)>0){
            foreach($clientCustomers as $customer){
                $customerArr = [];
                $customerNameInd = 'customer_name_'.$customer->id;
                $customer_url  = 'customer_link_url_'.$customer->id;
                $customer_img  = 'customer_image_'.$customer->id;
                if(in_array($customerNameInd, $ArrVal) && !empty($request->get($customerNameInd))){
                	$customerArr['name'] = $request->get($customerNameInd);
                }

                if(in_array($customer_url, $ArrVal) && !empty($request->get($customer_url))){
                	$customerArr['url'] = $request->get($customer_url);
                }

                if(in_array($customer_img, $ArrVal) && !empty($request->file($customer_img))){
                    $customerImage = $request->file($customer_img)->getClientOriginalName();
                    $customerImageFolder = "client_images/".$clientName."/"."customerImages/";

                    if(!is_dir($customerImageFolder)){
                        mkdir($customerImageFolder, 0755, true);
                    }
                    $customerImagePath = $customerImageFolder ."/". $customerImage;
                    if(file_exists($customerImagePath)){
                        unlink($customerImagePath);
                    } elseif(!empty($customer->id) && file_exists($customer->image)){
                        // unlink($customer->image);
                    }
                    $request->file($customer_img)->move($customerImageFolder, $customerImage);
                    $customerArr['image'] = $customerImagePath;
                    // open image
                    $img = Image::make($customerImagePath);
                    // enable interlacing
                    $img->interlace(true);
                    // save image interlaced
                    $img->save();
                }
                if(count($customerArr) > 0){
                    $customer->update($customerArr);
                }
            }

		}
		return 'true';
    }

    protected static function getClientCustomer($subdomain){
    	return static::join('clients', 'clients.id', '=', 'client_customers.client_id')
    					->where('clients.subdomain', $subdomain)->select('client_customers.*')->get();
    }

    protected static function addCustomer($client){
        $customers = ['SSGMCE','GATE THE Direction','Kaizen Technology', 'Last Hours Technology'];
        $customerUrls = ['SSGMCE' => 'http://ssgmce.org/Default.aspx?ReturnUrl=%2f','GATE THE Direction' => 'http://gatethedirection.com/','Kaizen Technology' => 'http://kaizenn.org/', 'Last Hours Technology' => 'http://lasthourstech.com/'];
        $customerImages = ['SSGMCE' => 'images/logo/ssgmce.jpg','GATE THE Direction' => 'images/logo/gate-the-Direction.png','Kaizen Technology' => 'images/logo/kaizen.jpg', 'Last Hours Technology' => 'images/logo/lasthour-logo.jpg'];
        foreach($customers as $customer){
            $customerObj = new static;
            $customerObj->name = $customer;
            $customerObj->url = $customerUrls[$customer];
            $customerObj->client_id = $client->id;
            $customerObj->image = $customerImages[$customer];;
            $customerObj->save();
        }
        return 'true';
    }
}
