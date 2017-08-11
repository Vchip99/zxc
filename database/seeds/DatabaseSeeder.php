<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert(['id' => 1,'name' =>  'vchip','email' =>  'vchip@gmail.com','password' =>  '$2y$10$upfj4OcAkFk9/BnOpz74Mupq2YjukCMCyBnOSCptnSLJrrZvh45d.','remember_token' =>  '5IAsf1KBEOErAH03YTqdT9TF9obISxBAU2URsqwGV6ToT64kSvwiaxXgAz5r','created_at' =>  NULL,'updated_at' =>  '2017-08-07 05:35:03']);

        DB::table('permissions')->insert([
            ['id' => 1,'name' => 'manageOnlineTest','slug' => 'manageOnlineTest','description' => 'Manage Online Test','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 2,'name' => 'manageOnlineCourse','slug' => 'manageOnlineCourse','description' => 'Manage Online Course','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 3,'name' => 'manageVkit','slug' => 'manageVkit','description' => 'Manage Vkit','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 4,'name' => 'manageDocument','slug' => 'manageDocument','description' => 'Manage Documen','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 5,'name' => 'manageBlog','slug' => 'manageBlog','description' => 'Manage Blog','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 6,'name' => 'manageLiveCourse','slug' => 'manageLiveCourse','description' => 'Manage Live Course','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 7,'name' => 'manageDiscussion','slug' => 'manageDiscussion','description' => 'manage Discussion','model' => NULL,'created_at' => NULL,'updated_at' => NULL],
        ]);

        DB::table('admin_permission')->insert([
            ['id' => 1,'permission_id' => 1,'admin_id' => 1,'created_at' => '2017-03-10 22:26:42','updated_at' => '2017-03-10 22:26:42'],
            ['id' => 2,'permission_id' => 2,'admin_id' => 1,'created_at' => '2017-03-10 22:26:42','updated_at' => '2017-03-10 22:26:42'],
            ['id' => 3,'permission_id' => 3,'admin_id' => 1,'created_at' => '2017-03-10 22:26:43','updated_at' => '2017-03-10 22:26:43'],
            ['id' => 4,'permission_id' => 4,'admin_id' => 1,'created_at' => '2017-03-10 22:56:19','updated_at' => '2017-03-10 22:56:19'],
            ['id' => 5,'permission_id' => 5,'admin_id' => 1,'created_at' => '2017-03-10 22:56:31','updated_at' => '2017-03-10 22:56:31'],
            ['id' => 6,'permission_id' => 6,'admin_id' => 1,'created_at' => '2017-03-10 22:56:44','updated_at' => '2017-03-10 22:56:44'],
            ['id' => 7,'permission_id' => 7,'admin_id' => 1,'created_at' => '2017-07-27 00:33:13','updated_at' => '2017-07-27 00:33:13']
        ]);

        DB::table('roles')->insert([
            ['id' => 1,'name' => 'admin','slug' => 'admin','description' => 'admin','level' => 1,'created_at' => NULL,'updated_at' => NULL],
            ['id' => 2,'name' => 'sub-admin','slug' => 'sub-admin','description' => 'sub-admin','level' => 2,'created_at' => NULL,'updated_at' => NULL],
        ]);

        DB::table('admin_role')->insert([
            ['id' => 1,'role_id' => 1,'admin_id' => 1,'created_at' => NULL,'updated_at' => NULL],
        ]);
    }
}
