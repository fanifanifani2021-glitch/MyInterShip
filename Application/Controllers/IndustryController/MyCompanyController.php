<?php
namespace Application\Controllers\IndustryController;

use Controller;
use Request;
use Response;
use Session;
use Validator;
use Notification;
use DB;
use Exception;
use PDO;

class MyCompanyController extends Controller
{
    private $idCompany;
    private $userType;

    public function __construct()
    {
        $this->idCompany = Session::get('id_company') ?? ($_SESSION['id_company'] ?? null);
        $this->userType  = Session::get('user_type') ?? ($_SESSION['user_type'] ?? null);

        if (!$this->idCompany) {
            Notification::swal2('Session Expired', 'Company session invalid. Please re-login.', 'warning');
            Response::redirect(router('login'))->send();
            exit;
        }
    }

    /**
     * 1. Halaman Detail Profil Perusahaan
     */
    public function index()
    {
        try {
            $db = DB::connection();

            // Fetch Company Data
            $stmt = $db->prepare("SELECT * FROM company WHERE id_company = :id LIMIT 1");
            $stmt->execute(['id' => $this->idCompany]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$company) {
                Notification::swal2('Error', 'Company record not found in system.', 'error');
                Response::back()->send();
                exit;
            }

            renderView('industry.my_company', [
                'title'        => 'My Company',
                'routes'       => 'my_company',
                'route'        => 'my_company',
                'company_data' => $company
            ]);

        } catch (Exception $e) {
            error_log("My Company Index Error: " . $e->getMessage());
            Notification::swal2('Error', 'Unable to fetch company information.', 'error');
            Response::back()->send();
            exit;
        }
    }

    /**
     * 2. Proses Edit Data Perusahaan (HRD Only)
     */
    public function update()
    {
        // Otorisasi: Hanya HRD yang diperbolehkan mengubah profil perusahaan
        if (strtoupper($this->userType) !== 'HRD') {
            Notification::swal2('Forbidden', 'Only HRD personnel are authorized to edit company profiles.', 'error');
            Response::back()->send();
            exit;
        }

        $rules = [
            'name'         => 'required|min:3',
            'company_type' => 'required',
            'phone'        => 'required',
            'email'        => 'required|email',
            'address'      => 'required',
            'province'     => 'required',
            'city'         => 'required',
            'description'  => 'required'
        ];

        $messages = [
            'name.required'         => 'Company name is required.',
            'company_type.required' => 'Please select a company type/industry sector.',
            'phone.required'        => 'Phone number is required.',
            'email.required'        => 'Email address is required.',
            'email.email'           => 'Please provide a valid email address.',
            'address.required'      => 'Company address is required.',
            'province.required'     => 'Province is required.',
            'city.required'         => 'City is required.',
            'description.required'  => 'Company description is required.'
        ];

        $validator = Validator::make(Request::all(), $rules, [], $messages);

        if ($validator->fails()) {
            Session::flash('validation_errors', $validator->errors());
            Session::flash('old', Request::all());
            Session::flash('ModalEdit', true); // Pemicu modal otomatis terbuka di Blade jika gagal
            
            Notification::swal2('Validation Error', 'Please check your inputs and try again.', 'error');
            Response::back()->send();
            exit;
        }

        try {
            // Tentukan Tipe Perusahaan (Jika memilih "Other")
            $typeInput  = Request::input('company_type');
            $typeOther  = Request::input('company_type_other');
            $finalType  = ($typeInput === 'Other' && !empty($typeOther)) ? $typeOther : $typeInput;

            $db = DB::connection();
            $stmt = $db->prepare("
                UPDATE company SET 
                    name        = :name,
                    type        = :type,
                    phone       = :phone,
                    email       = :email,
                    linkedin    = :linkedin,
                    website     = :website,
                    facebook    = :facebook,
                    twitter     = :twitter,
                    instagram   = :instagram,
                    logo        = :logo,
                    address     = :address,
                    province    = :province,
                    city        = :city,
                    description = :description,
                    updated_at  = NOW()
                WHERE id_company = :id
            ");

            $stmt->execute([
                'name'        => Request::input('name'),
                'type'        => $finalType,
                'phone'       => Request::input('phone'),
                'email'       => Request::input('email'),
                'linkedin'    => Request::input('linkedin', ''),
                'website'     => Request::input('website', ''),
                'facebook'    => Request::input('facebook', ''),
                'twitter'     => Request::input('twitter', ''),
                'instagram'   => Request::input('instagram', ''),
                'logo'        => Request::input('logo', ''),
                'address'     => Request::input('address'),
                'province'    => Request::input('province'),
                'city'        => Request::input('city'),
                'description' => Request::input('description'),
                'id'          => $this->idCompany
            ]);

            // Sync Update Session Name jika berubah
            $_SESSION['company_name'] = Request::input('name');
            Session::set('company_name', Request::input('name'));

            Notification::swal2('Updated!', 'Company profile updated successfully.', 'success');
            Response::redirect(router('industry.my_company'))->send();
            exit;

        } catch (Exception $e) {
            error_log("Update Company Profile Error: " . $e->getMessage());
            Session::flash('old', Request::all());
            Session::flash('ModalEdit', true);
            
            Notification::swal2('Error', 'An internal error occurred while updating company profile.', 'error');
            Response::back()->send();
            exit;
        }
    }
}