<?php
    defined ('KAFKA_TOPIC') || define('KAFKA_TOPIC', 'vtest_first_laminas');
    
    // Redis cache configs
    defined ('REDIS_CONFIG') || define('REDIS_CONFIG', require 'redis.configs.php');
    
    // Upload
    defined ('ROOT_PUBLIC_PATH') || define('ROOT_PUBLIC_PATH', PUBLIC_PATH);
    defined ('ROOT_UPLOAD_PATH') || define('ROOT_UPLOAD_PATH', PUBLIC_PATH . '/uploads' );

    // Message
    defined ('ZF_MSG_DATA_NOT_EXISTS') || define('ZF_MSG_DATA_NOT_EXISTS', 'Không tìm thấy dữ liệu');
    defined ('ZF_MSG_UPDATE_SUCCESS') || define('ZF_MSG_UPDATE_SUCCESS', 'Cập nhật dữ liệu thành công');
    defined ('ZF_MSG_UPDATE_FAIL') || define('ZF_MSG_UPDATE_FAIL', 'Cập nhật dữ liệu thất bại');
    defined ('ZF_MSG_ADD_FAIL') || define('ZF_MSG_ADD_FAIL', 'Thêm dữ liệu thất bại');
    defined ('ZF_MSG_ADD_SUCCESS') || define('ZF_MSG_ADD_SUCCESS', 'Thêm dữ liệu thành công');
    defined ('ZF_MSG_DUPLICATE_FAIL') || define('ZF_MSG_DUPLICATE_FAIL', 'Nhân bản thất bại');
    defined ('ZF_MSG_DUPLICATE_SUCCESS') || define('ZF_MSG_DUPLICATE_SUCCESS', 'Nhân bản thành công');
    defined ('ZF_MSG_SAVE_ORDER_SUCCESS') || define('ZF_MSG_SAVE_ORDER_SUCCESS', 'Thay đổi đơn hàng thành công');
    defined ('ZF_MSG_SAVE_ORDER_FAIL') || define('ZF_MSG_SAVE_ORDER_FAIL', 'Thay đổi đơn hàng thất bại');
    defined ('ZF_MSG_DEL_SUCCESS') || define('ZF_MSG_DEL_SUCCESS', 'Xoá dữ liệu thành công');
    defined ('ZF_MSG_DEL_FAIL') || define('ZF_MSG_DEL_FAIL', 'Xoá dữ liệu thất bại');
    defined ('ZF_MSG_NOT_EMPTY') || define('ZF_MSG_NOT_EMPTY', 'Vui lòng nhập dữ liệu cần thiết');
    defined ('ZF_MSG_REFRESH_CACHE_SUCCESS') || define('ZF_MSG_REFRESH_CACHE_SUCCESS', 'Làm mới bộ đệm thành công');
    defined ('ZF_MSG_REFRESH_CACHE_FAIL') || define('ZF_MSG_REFRESH_CACHE_FAIL', 'Làm mới bộ đệm thất bại');
    defined ('ZF_MSG_UPLOAD_SUCCESS') || define('ZF_MSG_UPLOAD_SUCCESS', 'Upload thành công');
    defined ('ZF_MSG_UPLOAD_FAIL') || define('ZF_MSG_UPLOAD_FAIL', 'Upload thất bại');
    defined ('ZF_MSG_NOT_ALLOW') || define('ZF_MSG_NOT_ALLOW', 'Phương thức không hợp lệ.');
    defined ('ZF_MSG_WENT_WRONG') || define('ZF_MSG_WENT_WRONG', 'Có lỗi xảy ra, vui lòng thử lại.');
    defined ('ZF_MSG_CONFIRM') || define('ZF_MSG_CONFIRM', 'Bạn có chắc chắn muốn thay đổi không?');
    defined ('ZF_MSG_MIN_LENGTH') || define('ZF_MSG_MIN_LENGTH', 'Độ dài phải có ít nhất {num} ký tự');
    defined ('ZF_MSG_MAX_LENGTH') || define('ZF_MSG_MAX_LENGTH', 'Độ dài phải ít hơn {num} ký tự');
    defined ('ZF_MSG_EMAIL_INVALID') || define('ZF_MSG_EMAIL_INVALID', 'Email không đúng định dạng');
    defined ('ZF_MSG_NOT_MATCH') || define('ZF_MSG_NOT_MATCH', 'Trường xác nhận không khớp');
    defined ('ZF_MSG_PHONE_INVALID') || define('ZF_MSG_PHONE_INVALID', 'Số điện thoại không đúng');
    defined ('ZF_MSG_ALIAS_INVALID') || define('ZF_MSG_ALIAS_INVALID', 'Alias không đúng định dạng');
    defined ('ZF_MSG_PASSWORD_INVALID') || define('ZF_MSG_PASSWORD_INVALID', 'Mật khẩu không đúng định dạng');
    defined ('ZF_MSG_REQUIRE_DATA') || define('ZF_MSG_REQUIRE_DATA', 'Vui lòng nhập đầy đủ thông tin');

    defined ('NO_REPLY_EMAIL') || define('NO_REPLY_EMAIL', 'tungts11.fpt@gmail.com');
    defined ('SIGN_UP_EMAIL') || define('SIGN_UP_EMAIL', 'tungts10.fpt@gmail.com');
    
    defined ('CUSTOMER_KEY') || define('CUSTOMER_KEY', 'customer');
    
    // System configs
    defined ('MAIN_DOMAIN') || define('MAIN_DOMAIN', 'kietdecor.local');
    defined ('MANAGER_DOMAIN') || define('MANAGER_DOMAIN', 'manager.kietdecor.local');
    defined ('FULL_MAIN_DOMAIN') || define('FULL_MAIN_DOMAIN', 'https://www.' . MAIN_DOMAIN);
    defined ('FULL_MAIN_DOMAIN_MANAGER') || define('FULL_MAIN_DOMAIN_MANAGER', 'https://www.' . MANAGER_DOMAIN);
    
    defined ('DISPLAY_APP_VERSION') || define('DISPLAY_APP_VERSION', 'vtest');
    defined ('SESSION_MAX_LIFETIME') || define('SESSION_MAX_LIFETIME', 604800);
    defined ('API_CB_DOMAIN') || define('API_CB_DOMAIN', [
        'kietdecor.local'    => 'manager.kietdecor.local',
        'kietdecor.net'      => 'manager.kietdecor.net',
        'test.kietdecor.net' => 'test.kietdecor.net/manager',
    ]);
    require 'custom/application.constant.php';
?>
