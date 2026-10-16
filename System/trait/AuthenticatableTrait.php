<?php

trait AuthenticatableTrait
{
    /**
     * Menjalankan proses pembuatan session dan redirect.
     * @param bool $isPersonify Jika true, data disimpan di 'user_personification'
     */
    public function establishSession(bool $isPersonify = false)
    {
        // 1. Ambil list kolom untuk session
        $fields = property_exists($this, 'sessionFields') ? $this->sessionFields : array_keys($this->attributes);
        
        $sessionData = [];
        foreach ($fields as $field) {
            if (isset($this->attributes[$field])) {
                $sessionData[$field] = $this->attributes[$field];
            }
        }

        // 2. Tambahkan metadata wajib
        $role = property_exists($this, 'sessionRole') ? $this->sessionRole : 'Guest';
        $sessionData['role'] = $role;
        $sessionData['name'] = $this->attributes['name'] ?? ($this->attributes['user_fullname'] ?? 'User');
        $sessionData['is_personified_at'] = date('Y-m-d H:i:s');

        // 3. Logika Penyimpanan
        if ($isPersonify) {
            // SIMPAN KE INDEX KHUSUS (Sesuai keinginan kamu)
            Session::set('user_personification', $sessionData);
            Session::set('is_personifying', true);

            // Audit Log (PENTING: Gunakan $sessionData, bukan $data)
            if (class_exists('Logger')) {
                Logger::write('PERSONIFICATION_START', [
                    'admin'  => $_SESSION['name'] ?? 'System', 
                    'target' => $sessionData['name'],
                    'role'   => $role
                ]);
            }
            
            // JANGAN REGENERATE SESSION saat personify agar session CDC tidak hangus
        } else {
            // LOGIN NORMAL
            foreach ($sessionData as $key => $val) {
                Session::set($key, $val);
            }
            
            // Simpan Data Mentah Model
            $className = (new \ReflectionClass($this))->getShortName();
            $rawAttributes = $this->attributes;
            if (isset($rawAttributes['password'])) unset($rawAttributes['password']);
            
            Session::set($className . '_Data', $rawAttributes);
            Session::set($className . '-Data', $rawAttributes);

            Session::regenerate();
        }

        // 4. Cleanup Redirect Data
        $intended = Session::get('redirect_intended');
        Session::remove('redirect_intended');
        Session::remove('REDIRECT');

        // 5. Tentukan Target Redirect
        $target = $this->getLoginRedirectTarget($intended, $isPersonify);

        // 6. Handle Response (KHUSUS AJAX)
        if (Request::isAjax()) {
            // Bersihkan buffer agar tidak ada spasi/error yang ikut masuk ke JSON
            if (ob_get_length()) ob_clean(); 
            
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => true, 
                'message' => 'Welcome back, ' . $sessionData['name'],
                'target'  => $target // <--- Pastikan ini terkirim
            ]);
            exit; // Wajib exit agar tidak ada HTML yang nyangkut
        }

        // Standard Redirect untuk Login Biasa
        if (function_exists('make_snackbar')) {
            make_snackbar('Welcome back, ' . $sessionData['name']);
        }
        
        Response::redirect($target);
        exit; 
    }

    protected function getLoginRedirectTarget($intended, $isPersonify)
    {
        // Personifikasi selalu ke dashboard utama target
        if ($isPersonify) return Url::base('dashboard');
        
        if (!empty($intended)) return $intended;

        if (method_exists($this, 'redirectAfterLogin')) {
            return $this->redirectAfterLogin();
        }

        return Url::base('dashboard');
    }
}