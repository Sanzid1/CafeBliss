const deliveryType = document.getElementById('deliveryType');
const addressField = document.getElementById('addressField');

deliveryType.addEventListener('change', function() {
    if (this.value === 'Delivery') {
        addressField.style.display = 'block';
    } else {
        addressField.style.display = 'none';
    }
});
