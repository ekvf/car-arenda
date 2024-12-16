function updateCost() {
    const rentalDate = document.getElementById('rental_date').value;
    const returnDate = document.getElementById('return_date').value;
    const carId = <?php echo $rental['Cars_ID']; ?>; // Передаем ID автомобиля

    fetch(`/calculate_cost.php?car_id=${carId}&rental_date=${rentalDate}&return_date=${returnDate}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('total_cost').value = data.cost;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
