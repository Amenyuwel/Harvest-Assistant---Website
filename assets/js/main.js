//-- =========== Scripts =========  -->
    let toggle = document.querySelector(".toggle");
    let sidebar = document.querySelector(".sidebar");
    let main = document.querySelector(".main");

    toggle.onclick = function () {
        sidebar.classList.toggle("active");
        main.classList.toggle("active");
};


