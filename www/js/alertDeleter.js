setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(element) {
        element.remove();
    });
}, 5000); // 5000 ms = 5 sekund