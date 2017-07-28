<?php


Route::group(['domain' => 'localvchip.com'], function () {

	Route::get('/', 'HomeController@home');
	// admin course category
	Route::get('admin/manageCourseCategory', 'Course\CourseCategoryController@show');
	Route::get('admin/createCourseCategory', 'Course\CourseCategoryController@create');
	Route::post('admin/createCourseCategory', 'Course\CourseCategoryController@store');
	Route::get('admin/coursecategory/{id}/edit', 'Course\CourseCategoryController@edit');
	Route::put('admin/updateCourseCategory', 'Course\CourseCategoryController@update');
	Route::delete('admin/deleteCourseCategory', 'Course\CourseCategoryController@delete');

	// admin course sub category
	Route::get('admin/manageCourseSubCategory', 'Course\CourseSubCategoryController@show');
	Route::get('admin/createCourseSubCategory', 'Course\CourseSubCategoryController@create');
	Route::post('admin/createCourseSubCategory', 'Course\CourseSubCategoryController@store');
	Route::get('admin/coursesubcategory/{id}/edit', 'Course\CourseSubCategoryController@edit');
	Route::put('admin/updateCourseSubCategory', 'Course\CourseSubCategoryController@update');
	Route::delete('admin/deleteCourseSubCategory', 'Course\CourseSubCategoryController@delete');

	// admin course course
	Route::get('admin/manageCourseCourse', 'Course\CourseCourseController@show');
	Route::get('admin/createCourseCourse', 'Course\CourseCourseController@create');
	Route::post('admin/createCourseCourse', 'Course\CourseCourseController@store');
	Route::get('admin/courseCourse/{id}/edit', 'Course\CourseCourseController@edit');
	Route::put('admin/updateCourseCourse', 'Course\CourseCourseController@update');
	Route::delete('admin/deleteCourseCourse', 'Course\CourseCourseController@delete');
	Route::post('admin/getCourseSubCategories', 'Course\CourseCourseController@getCourseSubCategories');

	// admin course video
	Route::get('admin/manageCourseVideo', 'Course\CourseVideoController@show');
	Route::get('admin/createCourseVideo', 'Course\CourseVideoController@create');
	Route::post('admin/createCourseVideo', 'Course\CourseVideoController@store');
	Route::get('admin/courseVideo/{id}/edit', 'Course\CourseVideoController@edit');
	Route::put('admin/updateCourseVideo', 'Course\CourseVideoController@update');
	Route::delete('admin/deleteCourseVideo', 'Course\CourseVideoController@delete');

	//Admin Login
	Route::get('admin/login', 'AdminAuth\LoginController@showLoginForm');
	Route::get('admin', 'AdminAuth\LoginController@redirectToLoginForm');
	Route::post('admin/login', 'AdminAuth\LoginController@login');
	Route::post('admin/logout', 'AdminAuth\LoginController@logout');

	// Admin
	Route::get('admin/home', 'Admin\AdminController@home');
	Route::get('admin/manageSendMails', 'Admin\AdminController@writeMail');
	Route::post('admin/sendSubscribedMails', 'Admin\AdminController@sendSubscribedMails');
	Route::get('admin/manageClients', 'Admin\AdminController@manageClients');
	Route::post('admin/changeClientPermissionStatus', 'Admin\AdminController@changeClientPermissionStatus');
	Route::delete('admin/deleteClient', 'Admin\AdminController@deleteClient');


	// Admin all users
	Route::get('admin/allUsers', 'Admin\AllUsersInfoController@allUsers');
	Route::post('admin/showOtherStudents', 'Admin\AllUsersInfoController@showOtherStudents');
	Route::post('admin/deleteStudent', 'Admin\AllUsersInfoController@deleteStudent');
	Route::post('admin/getDepartments', 'Admin\AllUsersInfoController@getDepartments');
	Route::post('admin/changeOtherStudentApproveStatus', 'Admin\AllUsersInfoController@changeOtherStudentApproveStatus');
	Route::post('admin/searchUsers', 'Admin\AllUsersInfoController@searchUsers');
	Route::get('admin/userTestResults/{id?}', 'Admin\AllUsersInfoController@userTestResults');
	Route::post('admin/showUserTestResults', 'Admin\AllUsersInfoController@showUserTestResults');
	Route::get('admin/userCourses/{id?}', 'Admin\AllUsersInfoController@userCourses');
	Route::post('admin/showUserCourses', 'Admin\AllUsersInfoController@showUserCourses');
	Route::get('admin/userPlacement/{id?}', 'Admin\AllUsersInfoController@userPlacement');
	Route::post('admin/getStudentById', 'Admin\AllUsersInfoController@getStudentById');
	Route::get('admin/userVideo/{id?}', 'Admin\AllUsersInfoController@userVideo');
	Route::put('admin/updateStudentVideo', 'Admin\AllUsersInfoController@updateStudentVideo');


	// admin college info
	Route::get('admin/manageCollegeInfo', 'Test\CollegeInfo@manageCollegeInfo');
	Route::get('admin/createCollege', 'Test\CollegeInfo@create');
	Route::post('admin/createCollege', 'Test\CollegeInfo@store');
	Route::get('admin/college/{id}/edit', 'Test\CollegeInfo@edit');
	Route::put('admin/updateCollege', 'Test\CollegeInfo@update');
	Route::delete('admin/deleteCollege', 'Test\CollegeInfo@delete');

	// user login
	Route::get('login', 'UserAuth\LoginController@showLoginForm');
	Route::post('login', 'UserAuth\LoginController@login');
	Route::post('/logout', 'UserAuth\LoginController@logout');

	//User Register
	// Route::get('register', 'UserAuth\RegisterController@showRegistrationForm');
	Route::post('register', 'UserAuth\RegisterController@register');
	Route::get('forgotPassword', 'UserAuth\ForgotPasswordController@showLinkRequestForm');
	Route::post('password/email', 'UserAuth\ForgotPasswordController@sendPasswordResetLink');
	Route::get('password/reset/{token}', 'UserAuth\ResetPasswordController@showResetForm');
	Route::post('password/reset', 'UserAuth\ResetPasswordController@reset');
	Route::get('register/verify/{token}', 'UserAuth\RegisterController@verify');
	Route::get('signup', 'HomeController@signup');

	Route::post('getDepartments', 'HomeController@getDepartments');


	// manage sub admin
	Route::get('admin/manageSubadminUser', 'Admin\SubadminController@show');
	Route::get('admin/createSubAdmin', 'Admin\SubadminController@create');
	Route::post('admin/createSubAdmin', 'Admin\SubadminController@store');
	Route::get('admin/subadmin/{id}/edit', 'Admin\SubadminController@edit');
	Route::put('admin/updateSubAdmin', 'Admin\SubadminController@update');
	// Route::get('admin/manageSubAdminHome','Admin\SubadminController@showSubAdminHome');
	// Route::post('admin/updateSubDomainHome','Admin\SubadminController@updateSubAdminHome');


	// // vchip
	// Route::get('admin', 'Test\AdminController@index');
	// Route::post('adminLogin', 'Test\AdminController@adminLogin');
	// Route::post('getAdminData', 'Test\AdminController@getAdminData');
	// Route::post('adminLogout', 'Test\AdminController@adminLogout');


	// admin test category
	Route::get('admin/manageCategory', 'Test\CategoryController@show');
	Route::get('admin/createCategory', 'Test\CategoryController@create');
	Route::post('admin/createCategory', 'Test\CategoryController@store');
	Route::get('admin/category/{id}/edit', 'Test\CategoryController@edit');
	Route::put('admin/updateCategory', 'Test\CategoryController@update');
	Route::delete('admin/deleteCategory', 'Test\CategoryController@delete');

	// admin test sub category
	Route::get('admin/manageSubCategory', 'Test\SubCategoryController@show');
	Route::get('admin/createSubCategory', 'Test\SubCategoryController@create');
	Route::post('admin/createSubCategory', 'Test\SubCategoryController@store');
	Route::get('admin/subCategory/{id}/edit', 'Test\SubCategoryController@edit');
	Route::put('admin/updateSubCategory', 'Test\SubCategoryController@update');
	Route::delete('admin/deleteSubCategory', 'Test\SubCategoryController@delete');
	Route::post('admin/getSubCategories', [ 'as' => 'admin/getSubCategories', 'uses' => 'Test\SubCategoryController@getSubCategories' ]);

	// admin test subject
	Route::get('admin/manageSubject', 'Test\SubjectController@show');
	Route::get('admin/createSubject', 'Test\SubjectController@create');
	Route::post('admin/createSubject', 'Test\SubjectController@store');
	Route::get('admin/subject/{id}/edit', 'Test\SubjectController@edit');
	Route::put('admin/updateSubject', 'Test\SubjectController@update');
	Route::delete('admin/deleteSubject', 'Test\SubjectController@delete');

	// admin  test paper
	Route::get('admin/managePaper', 'Test\PaperController@show');
	Route::get('admin/createPaper', 'Test\PaperController@create');
	Route::post('admin/createPaper', 'Test\PaperController@store');
	Route::get('admin/paper/{id}/edit', 'Test\PaperController@edit');
	Route::put('admin/updatePaper', 'Test\PaperController@update');
	Route::delete('admin/deletePaper', 'Test\PaperController@delete');
	Route::post('admin/getSubjectsByCatIdBySubcatId', [ 'as' => 'admin/getSubjectsByCatIdBySubcatId','uses' => 'Test\PaperController@getSubjectsByCatIdBySubcatId' ]);

	// admin  test questions
	Route::get('admin/manageQuestions', 'Test\QuestionController@index');
	Route::post('admin/showQuestions', 'Test\QuestionController@show');
	Route::get('admin/createQuestion', 'Test\QuestionController@create');
	Route::post('admin/createQuestion', 'Test\QuestionController@store');
	Route::get('admin/question/{id}/edit', 'Test\QuestionController@edit');
	Route::put('admin/updateQuestion', 'Test\QuestionController@update');
	Route::delete('admin/deleteQuestion', 'Test\QuestionController@delete');
	Route::post('admin/getPapersBySubjectId', [ 'as' => 'admin/getPapersBySubjectId','uses' => 'Test\QuestionController@getPapersBySubjectId' ]);
	Route::post('admin/getNextQuestionCount', [ 'as' => 'admin/getNextQuestionCount','uses' => 'Test\QuestionController@getNextQuestionCount' ]);
	Route::post('admin/getCurrentQuestionCount', [ 'as' => 'admin/getCurrentQuestionCount','uses' => 'Test\QuestionController@getCurrentQuestionCount' ]);
	Route::post('admin/getPrevQuestion', [ 'as' => 'admin/getPrevQuestion','uses' => 'Test\QuestionController@getPrevQuestion' ]);



	// // admin  User
	// Route::get('admin/manageUsers', 'Test\AdminController@show');
	// Route::get('admin/createUser', 'Test\AdminController@create');
	// Route::post('admin/createUser', 'Test\AdminController@store');
	// Route::get('admin/User/{id}/edit', 'Admin\AdminController@edit');
	// Route::put('admin/updateUser', 'Test\AdminController@update');
	// Route::delete('admin/deleteUser', 'Test\AdminController@delete');

	// verify account
	Route::get('verifyAccount', 'HomeController@verifyAccount');
	Route::post('verifyEmail', 'HomeController@verifyEmail');
	Route::post('subscribedUser', 'HomeController@subscribedUser');
	Route::get('register/verifySubscriedUser/{token}', 'HomeController@verifySubscriedUser');

	// home
	// Route::get('/', 'HomeController@home');
	// Route::get('/home', 'HomeController@home');
	Route::get('webinar', 'HomeController@webinar');
	Route::get('webinarerror', 'HomeController@webinarerror');
	Route::get('vEducation', 'HomeController@vEducation');
	Route::get('vConnect', 'HomeController@vConnect');
	Route::get('vPendrive', 'HomeController@vPendrive');
	Route::get('vCloud', 'HomeController@vCloud');
	Route::get('liveVideo', 'HomeController@liveVideo');
	Route::get('career', 'HomeController@career');
	Route::get('ourpartner', 'HomeController@ourpartner');
	Route::get('contactus', 'HomeController@contactus');
	Route::post('sendMail', 'HomeController@sendMail');
	Route::post('sendContactUsMail', 'HomeController@sendContactUsMail');

	// online courses front
	Route::get('courses', 'CourseController@courses');
	Route::post('getCourseByCatIdBySubCatId', 'CourseController@getCourseByCatIdBySubCatId');
	Route::post('getCourseSubCategories', 'CourseController@getCourseSubCategories');
	Route::get('courseDetails/{id}', 'CourseController@courseDetails');
	Route::post('registerCourse', 'CourseController@courseRegister');
	Route::get('episode/{id}', [ 'as' => 'episode', 'uses' => 'CourseController@episode' ]);
	Route::post('getOnlineCourseBySearchArray', 'CourseController@getOnlineCourseBySearchArray');
	Route::post('createCourseComment', 'CourseController@createCourseComment');
	Route::put('updateCourseComment', 'CourseController@updateCourseComment');
	Route::delete('deleteCourseComment', 'CourseController@deleteCourseComment');
	Route::post('createCourseSubComment', 'CourseController@createCourseSubComment');
	Route::put('updateCourseSubComment', 'CourseController@updateCourseSubComment');
	Route::delete('deleteCourseSubComment', 'CourseController@deleteCourseSubComment');
	Route::post('likeCourseVideo', 'CourseController@likeCourseVideo');
	Route::post('likeCourseVideoComment', 'CourseController@likeCourseVideoComment');
	Route::post('likeCourseVideoSubComment', 'CourseController@likeCourseVideoSubComment');


	/// user Post Comment
	Route::post('createAllPost',  'PostCommentController@createAllPost');
	Route::post('createAllPostComment',  'PostCommentController@createAllPostComment');
	Route::post('createAllSubComment',  'PostCommentController@createAllSubComment');
	Route::put('updateAllPost',  'PostCommentController@updateAllPost');
	Route::delete('deleteAllPost',  'PostCommentController@deleteAllPost');
	Route::put('updateAllComment',  'PostCommentController@updateAllComment');
	Route::delete('deleteAllComment',  'PostCommentController@deleteAllComment');
	Route::put('updateAllSubComment',  'PostCommentController@updateAllSubComment');
	Route::delete('deleteAllSubComment',  'PostCommentController@deleteAllSubComment');

	//Discussion category
	Route::get('admin/manageDiscussionCategory', 'Discussion\DiscussionCategoryController@show');
	Route::get('admin/createDiscussionCategory', 'Discussion\DiscussionCategoryController@create');
	Route::post('admin/createDiscussionCategory', 'Discussion\DiscussionCategoryController@store');
	Route::get('admin/discussionCategory/{id}/edit', 'Discussion\DiscussionCategoryController@edit');
	Route::put('admin/updateDiscussionCategory', 'Discussion\DiscussionCategoryController@update');
	Route::delete('admin/deleteDiscussionCategory', 'Discussion\DiscussionCategoryController@delete');

	// admin  vkit category
	Route::get('admin/manageVkitCategory', 'Vkit\VkitCategoryController@show');
	Route::get('admin/createVkitCategory', 'Vkit\VkitCategoryController@create');
	Route::post('admin/createVkitCategory', 'Vkit\VkitCategoryController@store');
	Route::get('admin/vkitCategory/{id}/edit', 'Vkit\VkitCategoryController@edit');
	Route::put('admin/updateVkitCategory', 'Vkit\VkitCategoryController@update');
	Route::delete('admin/deleteVkitCategory', 'Vkit\VkitCategoryController@delete');

	// admin vkit project
	Route::get('admin/manageVkitProject', 'Vkit\VkitProjectController@show');
	Route::get('admin/createVkitProject', 'Vkit\VkitProjectController@create');
	Route::post('admin/createVkitProject', 'Vkit\VkitProjectController@store');
	Route::get('admin/vkitProject/{id}/edit', 'Vkit\VkitProjectController@edit');
	Route::put('admin/updateVkitProject', 'Vkit\VkitProjectController@update');
	Route::delete('admin/deleteVkitProject','Vkit\VkitProjectController@delete');

	// vkits user
	Route::get('vkits', 'VkitController@show');
	Route::get('vkitproject/{id}', [  'as' => 'project','uses' => 'VkitController@vkitproject']);
	Route::post('getVkitProjectsByCategoryId', 'VkitController@getVkitProjectsByCategoryId');
	Route::post('getVkitProjectsBySearchArray', 'VkitController@getVkitProjectsBySearchArray');
	Route::post('registerProject', 'VkitController@registerProject');
	Route::post('createProjectComment', 'VkitController@createProjectComment');
	Route::delete('deleteVkitProjectComment', 'VkitController@deleteVkitProjectComment');
	Route::put('updateVkitProjectComment', 'VkitController@updateVkitProjectComment');
	Route::post('createVkitProjectSubComment', 'VkitController@createVkitProjectSubComment');
	Route::put('updateVkitProjectSubComment', 'VkitController@updateVkitProjectSubComment');
	Route::delete('deleteVkitProjectSubComment', 'VkitController@deleteVkitProjectSubComment');
	Route::post('likeVkitProject', 'VkitController@likeVkitProject');
	Route::post('likeVkitProjectComment', 'VkitController@likeVkitProjectComment');
	Route::post('likekitProjectSubComment', 'VkitController@likekitProjectSubComment');

	// blog
	Route::get('blog', 'BlogController@show');
	Route::get('blogComment/{id}', [ 'as' => 'blogComment', 'uses' => 'BlogController@blogComment']);
	Route::post('createBlogComment', 'BlogController@createBlogComment');
	Route::post('createBlogSubComment', 'BlogController@createBlogSubComment');
	Route::post('getBlogsByCategoryId', 'BlogController@getBlogsByCategoryId');
	Route::get('getBlogsByCategoryId', 'BlogController@getBlogsByCategoryId');
	Route::put('updateBlogComment', 'BlogController@updateBlogComment');
	Route::delete('deleteBlogComment', 'BlogController@deleteBlogComment');
	Route::put('updateBlogSubComment', 'BlogController@updateBlogSubComment');
	Route::delete('deleteBlogSubComment', 'BlogController@deleteBlogSubComment');
	Route::get('tagBlogs/{id}', 'BlogController@tagBlogs');


	// admin blog category
	Route::get('admin/manageBlogCategory', 'Blog\AdminBlogCategoryController@show');
	Route::get('admin/createBlogCategory', 'Blog\AdminBlogCategoryController@create');
	Route::post('admin/createBlogCategory', 'Blog\AdminBlogCategoryController@store');
	Route::get('admin/blogCategory/{id}/edit', 'Blog\AdminBlogCategoryController@edit');
	Route::put('admin/updateBlogCategory', 'Blog\AdminBlogCategoryController@update');
	Route::delete('admin/deleteBlogCategory', 'Blog\AdminBlogCategoryController@delete');

	// admin blog
	Route::get('admin/manageBlog', 'Blog\AdminBlogController@show');
	Route::get('admin/createBlog', 'Blog\AdminBlogController@create');
	Route::post('admin/createBlog', 'Blog\AdminBlogController@store');
	Route::get('admin/blog/{id}/edit', 'Blog\AdminBlogController@edit');
	Route::put('admin/updateBlog', 'Blog\AdminBlogController@update');
	Route::delete('admin/deleteBlog', 'Blog\AdminBlogController@delete');

	// admin live courses
	Route::get('admin/manageLiveCourse', 'LiveCourse\LiveCourseController@show');
	Route::get('admin/createLiveCourse', 'LiveCourse\LiveCourseController@create');
	Route::post('admin/createLiveCourse', 'LiveCourse\LiveCourseController@store');
	Route::get('admin/liveCourse/{id}/edit', 'LiveCourse\LiveCourseController@edit');
	Route::put('admin/updateLiveCourse', 'LiveCourse\LiveCourseController@update');
	Route::delete('admin/deleteLiveCourses', 'LiveCourse\LiveCourseController@delete');

	// admin live videos
	Route::get('admin/manageLiveVideo', 'LiveCourse\LiveVideoController@show');
	Route::get('admin/createLiveVideo', 'LiveCourse\LiveVideoController@create');
	Route::post('admin/createLiveVideo', 'LiveCourse\LiveVideoController@store');
	Route::get('admin/liveVideo/{id}/edit', 'LiveCourse\LiveVideoController@edit');
	Route::put('admin/updateLiveVideo', 'LiveCourse\LiveVideoController@update');
	Route::delete('admin/deleteLiveVideo', 'LiveCourse\LiveVideoController@delete');

	// admin Documents Category
	Route::get('admin/manageDocumentsCategory', 'Documents\DocumentsCategoryController@show');
	Route::get('admin/createDocumentsCategory', 'Documents\DocumentsCategoryController@create');
	Route::post('admin/createDocumentsCategory', 'Documents\DocumentsCategoryController@store');
	Route::get('admin/documentCategory/{id}/edit', 'Documents\DocumentsCategoryController@edit');
	Route::put('admin/updateDocumentsCategory', 'Documents\DocumentsCategoryController@update');
	Route::delete('admin/deleteDocumentsCategory', 'Documents\DocumentsCategoryController@delete');

	// admin Documents Docs
	Route::get('admin/manageDocumentsDoc', 'Documents\DocumentsDocController@show');
	Route::get('admin/createDocumentsDoc', 'Documents\DocumentsDocController@create');
	Route::post('admin/createDocumentsDoc', 'Documents\DocumentsDocController@store');
	Route::get('admin/documentDoc/{id}/edit', 'Documents\DocumentsDocController@edit');
	Route::put('admin/updateDocumentsDoc', 'Documents\DocumentsDocController@update');
	Route::delete('admin/deleteDocumentsDoc', 'Documents\DocumentsDocController@delete');

	// Documents Docs user front
	Route::get('documents', 'DocumentsController@show');
	Route::post('getDocumentsByCategoryId', 'DocumentsController@getDocumentsByCategoryId');
	Route::post('getDocumentsBySearchArray', 'DocumentsController@getDocumentsBySearchArray');
	Route::post('registerDocuments', 'DocumentsController@registerDocuments');
	Route::post('registerFavouriteDocuments', 'DocumentsController@registerFavouriteDocuments');

	// discussion and comments front
	Route::get('discussion', 'DiscussionController@discussion');
	Route::post('createPost', 'DiscussionController@createPost');
	Route::post('createComment', 'DiscussionController@createComment');
	Route::post('createSubComment', 'DiscussionController@createSubComment');
	Route::post('getDiscussionPostsByCategoryId', 'DiscussionController@getDiscussionPostsByCategoryId');
	Route::post('getDuscussionPostsBySearchArray', 'DiscussionController@getDuscussionPostsBySearchArray');
	Route::delete('deleteSubComment', 'DiscussionController@deleteSubComment');
	Route::delete('deleteComment', 'DiscussionController@deleteComment');
	Route::delete('deletePost', 'DiscussionController@deletePost');
	Route::put('updatePost', 'DiscussionController@updatePost');
	Route::put('updateComment', 'DiscussionController@updateComment');
	Route::put('updateSubComment', 'DiscussionController@updateSubComment');
	Route::post('goToPost', 'DiscussionController@goToPost');
	Route::post('goToComment', 'DiscussionController@goToComment');


	// live course front
	Route::get('liveCourse', 'LiveCourseVideoController@show');
	Route::get('liveCourse/{id}', 'LiveCourseVideoController@showLiveCourse');
	Route::get('liveEpisode/{id}', [ 'as' => 'liveEpisode', 'uses' => 'LiveCourseVideoController@showLiveEpisode']);
	Route::post('getLiveCourseByCatId', 'LiveCourseVideoController@getLiveCourseByCatId');
	Route::post('getLiveCourseBySearchArray', 'LiveCourseVideoController@getLiveCourseBySearchArray');
	Route::get('saveTimeSecurity', 'LiveCourseVideoController@saveTimeSecurity');
	Route::post('registerLiveCourse', 'LiveCourseVideoController@registerLiveCourse');
	Route::post('createLiveCourseComment', 'LiveCourseVideoController@createLiveCourseComment');
	Route::put('updateLiveCourseComment', 'LiveCourseVideoController@updateLiveCourseComment');
	Route::delete('deleteLiveCourseComment', 'LiveCourseVideoController@deleteLiveCourseComment');
	Route::post('createLiveCourseSubComment', 'LiveCourseVideoController@createLiveCourseSubComment');
	Route::put('updateLiveCourseSubComment', 'LiveCourseVideoController@updateLiveCourseSubComment');
	Route::delete('deleteLiveCourseSubComment', 'LiveCourseVideoController@deleteLiveCourseSubComment');
	Route::post('likeLiveVideo', 'LiveCourseVideoController@likeLiveVideo');
	Route::post('likeLiveVideoComment', 'LiveCourseVideoController@likeLiveVideoComment');
	Route::post('likeLiveVideoSubComment', 'LiveCourseVideoController@likeLiveVideoSubComment');

	// test front
	Route::get('/instructions', 'TestController@showInstructions');
	Route::get('online-tests', 'TestController@index');
	Route::get('showTest/{id}', 'TestController@showTest');
	Route::get('getTest/{id}', 'TestController@getTest');
	Route::post('showTests', 'TestController@showTests');
	Route::post('getSubCategories', [ 'as' => 'getSubCategories', 'uses' => 'TestController@getSubCategories' ]);
	Route::post('getDataByCatSubCat', [ 'as' => 'getDataByCatSubCat', 'uses' => 'TestController@getDataByCatSubCat' ]);
	Route::post('setSessions', 'TestController@setSessions');
	Route::post('registerPaper', 'TestController@registerPaper');
	Route::post('showUserTestResult', 'TestController@showUserTestResult');
	Route::post('isTestGiven', 'TestController@isTestGiven');


	// test Quiz front
	Route::post('/start-quiz', 'QuizController@startQuiz');
	Route::post('/questions', 'QuizController@getQuestions');
	Route::post('/getQuestions', 'QuizController@getAllQuestions');
	Route::post('/quiz-result', 'QuizController@getResult');
	Route::post('/solutions', 'QuizController@getSolutions');
	Route::get('/quiz-results', 'QuizController@getAllResults');
	Route::get('/user-results', 'QuizController@getUserResults');
	Route::post('showUserTestSolution', 'QuizController@showUserTestSolution');
	Route::get('downloadQuestions/{category}/{subcategory}/{subject}/{paper}', 'QuizController@downloadQuestions');

	// Account and Dashboard front
	Route::get('/account', 'AccountController@index');
	Route::get('/checkEmail', 'AccountController@checkEmail');
	Route::put('updatePassword', 'AccountController@updatePassword');
	Route::get('/profile', 'AccountController@showProfile');
	Route::get('myCourses', 'AccountController@myCourses');
	Route::get('myTest', 'AccountController@myTest');
	Route::get('myLiveCourses', 'AccountController@myLiveCourses');
	Route::get('myDocuments', 'AccountController@myDocuments');
	Route::get('myVkits', 'AccountController@myVkits');
	Route::get('myQuestions', [ 'as' => 'myQuestions', 'uses' => 'AccountController@myQuestions']);
	Route::get('myReplies', [ 'as' => 'myReplies', 'uses' => 'AccountController@myReplies']);
	Route::get('myCertificate', 'AccountController@myCertificate');
	Route::get('myFavouriteArticles', 'AccountController@myFavouriteArticles');
	Route::post('getFavouriteDocumentsByCategoryId', 'DocumentsController@getFavouriteDocumentsByCategoryId');
	Route::get('students', 'AccountController@students');
	Route::post('changeApproveStatus', 'AccountController@changeApproveStatus');
	Route::delete('deleteStudentFromCollege', 'AccountController@deleteStudentFromCollege');
	Route::post('searchStudent', 'AccountController@searchStudent');
	Route::get('studentTestResults/{id?}', 'AccountController@studentTestResults');
	Route::post('showTestResults', 'AccountController@showTestResults');
	Route::get('studentPlacement/{id?}', 'AccountController@studentPlacement');
	Route::get('studentCourses/{id?}', 'AccountController@studentCourses');
	Route::put('updateProfile', 'AccountController@updateProfile');
	Route::post('showStudentsByDepartmentByYear', 'AccountController@showStudentsByDepartmentByYear');
	Route::post('getStudentById', 'AccountController@getStudentById');
	Route::post('showStudentCourses', 'AccountController@showStudentCourses');
	Route::get('myCourseResults', 'AccountController@myCourseResults');
	Route::get('myTestResults', 'AccountController@myTestResults');
	Route::post('showUserTestResultsByCatBySubCat', 'AccountController@showUserTestResultsByCatBySubCat');

	// like- dis-like count front
	Route::post('likePost', 'CourseController@likePost');
	Route::post('likeComment', 'CourseController@likeComment');
	Route::post('likeSubComment', 'CourseController@likeSubComment');
	Route::post('discussionLikePost', 'DiscussionController@discussionLikePost');
	Route::post('discussionLikeComment', 'DiscussionController@discussionLikeComment');
	Route::post('likeBlogComment', 'BlogController@likeBlogComment');
	Route::post('likeBlogSubComment', 'BlogController@likeBlogSubComment');
	Route::post('discussionLikeSubComment', 'DiscussionController@discussionLikeSubComment');
	Route::post('likeBlog', 'BlogController@likeBlog');


});

Route::group(['domain' => '{client}.localvchip.com'], function () {
	Route::get('/', 'Client\ClientHomeController@clientHome');
	// Route::get('clientHome', 'ClientController@client');
	Route::get('client/login', 'ClientAuth\LoginController@showLoginForm');
  	Route::post('client/login', 'ClientAuth\LoginController@login');
  	Route::post('client/logout', 'ClientAuth\LoginController@logout');

  	// verify account
  	Route::get('verifyAccount', 'Client\ClientHomeController@verifyAccount');
  	Route::post('verifyClientEmail', 'Client\ClientHomeController@verifyClientEmail');

  	// client institute courses
  	Route::get('manageInstituteCourses', 'Client\InstituteCourse\ClientInstituteCourseController@show');
  	Route::get('createClientInstituteCourse', 'Client\InstituteCourse\ClientInstituteCourseController@create');
  	Route::post('createClientInstituteCourse', 'Client\InstituteCourse\ClientInstituteCourseController@store');
  	Route::get('clientInstituteCourse/{id}/edit', 'Client\InstituteCourse\ClientInstituteCourseController@edit');
  	Route::put('updateClientInstituteCourse', 'Client\InstituteCourse\ClientInstituteCourseController@update');
  	Route::delete('deleteClientInstituteCourse', 'Client\InstituteCourse\ClientInstituteCourseController@delete');

  	// client users info
  	Route::get('allUsers', 'Client\ClientUsersInfoController@allUsers');
  	Route::post('searchUsers', 'Client\ClientUsersInfoController@searchUsers');
  	Route::post('changeClientPermissionStatus', 'Client\ClientUsersInfoController@changeClientPermissionStatus');
  	Route::post('deleteStudent', 'Client\ClientUsersInfoController@deleteStudent');
  	Route::post('changeClientUserApproveStatus', 'Client\ClientUsersInfoController@changeClientUserApproveStatus');
  	Route::get('userTestResults/{id?}/{course?}', 'Client\ClientUsersInfoController@userTestResults');
  	Route::post('showUserTestResults', 'Client\ClientUsersInfoController@showUserTestResults');
  	Route::get('userCourses/{id?}/{course?}', 'Client\ClientUsersInfoController@userCourses');
  	Route::post('showUserCourses', 'Client\ClientUsersInfoController@showUserCourses');

  	// register client user
  	Route::post('/register', 'ClientuserAuth\RegisterController@register');
  	Route::post('/login', 'ClientuserAuth\LoginController@login');
	Route::post('/logout', 'ClientuserAuth\LoginController@logout');
	Route::get('register/verify/{token}', 'ClientuserAuth\RegisterController@verify');

  	// Route::get('client/home', 'Client\ClientBaseController@showDashBoard');
  	Route::get('manageClientHome', 'Client\ClientBaseController@manageClientHome');
	Route::put('updateClientHome', 'Client\ClientBaseController@updateClientHome');

  	// category
  	Route::get('manageOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@show');
  	Route::get('createOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@create');
  	Route::post('createOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@store');
  	Route::get('onlinecategory/{id}/edit', 'Client\OnlineCourse\ClientOnlineCategoryController@edit');
  	Route::put('updateOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@update');
  	Route::delete('deleteOnlineCategory', 'Client\OnlineCourse\ClientOnlineCategoryController@delete');

  	// sub category
  	Route::get('manageOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@show');
  	Route::get('createOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@create');
  	Route::post('createOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@store');
  	Route::get('onlinesubcategory/{id}/edit', 'Client\OnlineCourse\ClientOnlineSubCategoryController@edit');
  	Route::put('updateOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@update');
  	Route::delete('deleteOnlineSubCategory', 'Client\OnlineCourse\ClientOnlineSubCategoryController@delete');
  	Route::post('getOnlineCategories', 'Client\OnlineCourse\ClientOnlineSubCategoryController@getOnlineCategories');


  	// Online course
  	Route::get('manageOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@show');
  	Route::get('createOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@create');
  	Route::post('createOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@store');
  	Route::get('onlinecourse/{id}/edit', 'Client\OnlineCourse\ClientOnlineCourseController@edit');
  	Route::put('updateOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@update');
  	Route::delete('deleteOnlineCourse', 'Client\OnlineCourse\ClientOnlineCourseController@delete');

  	// Online video
  	Route::get('manageOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@show');
  	Route::get('createOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@create');
  	Route::post('createOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@store');
  	Route::get('onlinevideo/{id}/edit', 'Client\OnlineCourse\ClientOnlineVideoController@edit');
  	Route::put('updateOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@update');
  	Route::delete('deleteOnlineVideo', 'Client\OnlineCourse\ClientOnlineVideoController@delete');

  	// test category
  	Route::get('manageOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@show');
  	Route::get('createOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@create');
  	Route::post('createOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@store');
  	Route::get('onlinetestcategory/{id}/edit', 'Client\OnlineTest\ClientOnlineTestCategoryController@edit');
  	Route::put('updateOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@update');
  	Route::delete('deleteOnlineTestCategory', 'Client\OnlineTest\ClientOnlineTestCategoryController@delete');

  	// test sub category
  	Route::get('manageOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@show');
  	Route::get('createOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@create');
  	Route::post('createOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@store');
  	Route::get('onlinetestsubcategory/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@edit');
  	Route::put('updateOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@update');
  	Route::delete('deleteOnlineTestSubCategory', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@delete');
  	Route::post('getOnlineTestCategories', 'Client\OnlineTest\ClientOnlineTestSubCategoryController@getOnlineTestCategories');

  	// test subject
  	Route::get('manageOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@show');
  	Route::get('createOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@create');
  	Route::post('createOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@store');
  	Route::get('onlinetestsubject/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubjectController@edit');
  	Route::put('updateOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@update');
  	Route::delete('deleteOnlineTestSubject', 'Client\OnlineTest\ClientOnlineTestSubjectController@delete');
	Route::post('getOnlineSubjectsByCatIdBySubcatId', 'Client\OnlineTest\ClientOnlineTestSubjectController@getOnlineSubjectsByCatIdBySubcatId');

  	// test subject paper
  	Route::get('manageOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@show');
  	Route::get('createOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@create');
  	Route::post('createOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@store');
  	Route::get('onlinetestsubjectpaper/{id}/edit', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@edit');
  	Route::put('updateOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@update');
  	Route::delete('deleteOnlineTestSubjectPaper', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@delete');
  	Route::post('getOnlinePapersBySubjectId', 'Client\OnlineTest\ClientOnlineTestSubjectPaperController@getOnlinePapersBySubjectId');

  	// test question
  	Route::get('manageOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@index');
  	Route::post('showOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@show');
  	Route::get('createOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@create');
  	Route::post('createOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@store');
  	Route::get('onlinetestquestion/{id}/edit', 'Client\OnlineTest\ClientOnlineTestQuestionController@edit');
  	Route::put('updateOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@update');
  	Route::delete('deleteOnlineTestQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@delete');
  	Route::post('getClientNextQuestionCount', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientNextQuestionCount');
  	Route::post('getClientCurrentQuestionCount', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientCurrentQuestionCount');
  	Route::post('getClientPrevQuestion', 'Client\OnlineTest\ClientOnlineTestQuestionController@getClientPrevQuestion');

  	// online courses front
	Route::get('online-courses', 'Client\Front\ClientOnlineCourseFrontController@courses');
	Route::post('getOnlineCourseByCatIdBySubCatId', 'Client\Front\ClientOnlineCourseFrontController@getOnlineCourseByCatIdBySubCatId');
	Route::get('courseDetails/{id}', 'Client\Front\ClientOnlineCourseFrontController@courseDetails');
	Route::get('episode/{id}', [ 'as' => 'client.episode', 'uses' => 'Client\Front\ClientOnlineCourseFrontController@episode' ]);
  	Route::post('getOnlineSubCategories', 'Client\Front\ClientOnlineCourseFrontController@getOnlineSubCategories');
  	Route::post('registerClientUserCourse', 'Client\Front\ClientOnlineCourseFrontController@registerClientUserCourse');
  	Route::post('getRegisteredOnlineCourseByCatIdBySubCatId', 'Client\Front\ClientOnlineCourseFrontController@getRegisteredOnlineCourseByCatIdBySubCatId');
  	Route::post('createClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@createClientCourseComment');
  	Route::put('updateClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@updateClientCourseComment');
  	Route::delete('deleteClientCourseComment', 'Client\Front\ClientOnlineCourseFrontController@deleteClientCourseComment');
	Route::post('createClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@createClientCourseSubComment');
	Route::put('updateClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@updateClientCourseSubComment');
	Route::delete('deleteClientCourseSubComment', 'Client\Front\ClientOnlineCourseFrontController@deleteClientCourseSubComment');
	Route::post('likeClientCourseVideo', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideo');
	Route::post('likeClientCourseVideoComment', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideoComment');
	Route::post('likeClientCourseVideoSubComment', 'Client\Front\ClientOnlineCourseFrontController@likeClientCourseVideoSubComment');


  	// online tests front
	Route::get('online-tests', 'Client\Front\ClientOnlineTestFrontController@tests');
	Route::post('getOnlineTestSubCategories', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubCategories');
	Route::get('getTest/{id}', 'Client\Front\ClientOnlineTestFrontController@getTest');
	Route::post('getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion', 'Client\Front\ClientOnlineTestFrontController@getOnlineTestSubcategoriesByCategoryIdAssociatedWithQuestion');
	Route::post('getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion', 'Client\Front\ClientOnlineTestFrontController@getOnlineSubjectsAndPapersByCatIdBySubcatIdAssociatedWithQuestion');
	Route::post('setClientUserSessions', 'Client\Front\ClientOnlineTestFrontController@setClientUserSessions');
	Route::get('instructions', 'Client\Front\ClientOnlineTestFrontController@showInstructions');
	Route::post('registerClientUserPaper', 'Client\Front\ClientOnlineTestFrontController@registerClientUserPaper');
	Route::post('showUserTestResult', 'Client\Front\ClientOnlineTestFrontController@showUserTestResult');
	Route::post('isTestGiven', 'Client\Front\ClientOnlineTestFrontController@isTestGiven');

	// client online question front
	Route::post('questions', 'Client\Front\ClientOnlineQuestionFrontController@getQuestions');
	Route::post('quiz-result', 'Client\Front\ClientOnlineQuestionFrontController@getResult');
	Route::post('solutions', 'Client\Front\ClientOnlineQuestionFrontController@getSolutions');
	Route::post('getQuestions', 'Client\Front\ClientOnlineQuestionFrontController@getAllQuestions');
	Route::post('showUserTestSolution', 'Client\Front\ClientOnlineQuestionFrontController@showUserTestSolution');
	Route::get('downloadQuestions/{category}/{subcategory}/{subject}/{paper}', 'Client\Front\ClientOnlineQuestionFrontController@downloadQuestions');


	// client dashboard
	Route::get('dashboard', 'Client\ClientUserController@showClientUserDashBoard');
	Route::get('myCourses', 'Client\ClientUserController@myCourses');
	Route::get('myCertificate', 'Client\ClientUserController@myCertificate');
	Route::get('myTest', 'Client\ClientUserController@myTest');
	Route::get('myCourseResults', 'Client\ClientUserController@myCourseResults');
	Route::post('getCourseByCatIdBySubCatIdByUserId', 'Client\ClientUserController@getCourseByCatIdBySubCatIdByUserId');
	Route::get('myTestResults', 'Client\ClientUserController@myTestResults');
	Route::post('showUserTestResultsByCategoryBySubcategoryByUserId','Client\ClientUserController@showUserTestResultsByCategoryBySubcategoryByUserId');

	/// client user Post Comment
	Route::post('createClientAllPost',  'Client\ClientPostCommentController@createAllPost');
	Route::post('createClientAllPostComment',  'Client\ClientPostCommentController@createAllPostComment');
	Route::post('createClientAllChildComment',  'Client\ClientPostCommentController@createAllChildComment');
	Route::put('updateClientAllPost',  'Client\ClientPostCommentController@updateClientAllPost');
	Route::delete('deleteClientAllPost',  'Client\ClientPostCommentController@deleteClientAllPost');
	Route::post('createClientAllSubComment',  'Client\ClientPostCommentController@createClientAllSubComment');
	Route::put('updateClientAllSubComment',  'Client\ClientPostCommentController@updateClientAllSubComment');
	Route::delete('deleteClientAllSubComment',  'Client\ClientPostCommentController@deleteClientAllSubComment');
	Route::put('updateClientAllComment',  'Client\ClientPostCommentController@updateClientAllComment');
	Route::delete('deleteClientAllComment',  'Client\ClientPostCommentController@deleteClientAllComment');

	Route::post('clientLikePost', 'Client\Front\ClientOnlineCourseFrontController@likePost');
	Route::post('clientLikeComment', 'Client\Front\ClientOnlineCourseFrontController@clientLikeComment');
	Route::post('clientLikeSubComment', 'Client\Front\ClientOnlineCourseFrontController@clientLikeSubComment');
});
// Route::group(['domain' => 'clientuser.' . env('APP_DOMAIN')], function () {
//   Route::get('/login', 'ClientuserAuth\LoginController@showLoginForm');
//   Route::post('/login', 'ClientuserAuth\LoginController@login');
//   	Route::post('/logout', 'ClientuserAuth\LoginController@logout');

//   Route::get('/register', 'ClientuserAuth\RegisterController@showRegistrationForm');
//   Route::post('/register', 'ClientuserAuth\RegisterController@register');

//   Route::post('/password/email', 'ClientuserAuth\ForgotPasswordController@sendResetLinkEmail');
//   Route::post('/password/reset', 'ClientuserAuth\ResetPasswordController@reset');
//   Route::get('/password/reset', 'ClientuserAuth\ForgotPasswordController@showLinkRequestForm');
//   Route::get('/password/reset/{token}', 'ClientuserAuth\ResetPasswordController@showResetForm');
// });
