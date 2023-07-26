(function(){
    if (document.getElementById('edit-deschiption')) {
        var quill = new Quill('#edit-deschiption', {
            modules: {
                toolbar: [
                  ['bold', 'italic', 'underline',],
                  ['blockquote', 'code-block', 'image'],
                  [{ list: 'ordered' }, { list: 'bullet' }]
                ]
              },
              placeholder: 'Nhập thông tin mô tả về dịch vụ...',
            theme: 'snow' 
        });
    };

    Dropzone.autoDiscover = false;
    var serviceImg = new Dropzone("#serviceImg", {
        maxFilesize: 5, // Kích thước tối đa của mỗi tệp (đơn vị MB)
        maxFiles: 1, // Số lượng tệp tối đa mà người dùng có thể tải lên
        acceptedFiles: ".png, .jpg, .jpeg, .gif", // Loại tệp chấp nhận được
        addRemoveLinks: true, // Hiển thị liên kết xóa cho từng tệp đã tải lên
        dictDefaultMessage: "Thả tệp vào đây hoặc nhấp để tải lên", // Tin nhắn mặc định
        dictRemoveFile: "Xóa tệp", // Tin nhắn cho liên kết xóa
        init: function() {
            this.on("success", function(file, response) {
                // Xử lý khi tải lên thành công
                console.log(file, response);
            });
            this.on("error", function(file, errorMessage) {
                // Xử lý khi tải lên thất bại
                console.log(file, errorMessage);
            });
        }
    });

})()