<style>
.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #344047;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
}

select.form-control {
    height: 38px;
    background-color: white;
}

.form-control:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.contact-input-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #ced4da;
    border-radius: 4px;
    overflow: hidden;
}

.contact-input-wrapper .prefix {
    padding: 8px 12px;
    background: #f8f9fa;
    border-right: 1px solid #ced4da;
    color: #495057;
    font-size: 14px;
}

.contact-input-wrapper input {
    border: none;
    flex: 1;
    padding: 8px 12px;
    width: 100%;
}

.contact-input-wrapper input:focus {
    outline: none;
}

.contact-input-wrapper:focus-within {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.contact-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border-radius: 4px;
    font-weight: 500;
    cursor: pointer;
    border: none;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-primary {
    background-color: #07353f;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.address-fields {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.address-fields select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
    background-color: white;
}

.address-fields select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}
</style>

<form method="POST" onsubmit="handleBookingSubmit(this, event)">
    <div class="form-row">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($name) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($email) ?>" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="location">Complete Address</label>
            <div class="address-fields">
                <input type="text" class="form-control" name="house_number" id="house_number" placeholder="House/Unit Number" required onchange="updateLocation()">
                <input type="text" class="form-control" name="street" id="street" placeholder="Street Name" required onchange="updateLocation()">
                <select name="district" id="district" class="form-control" required onchange="updateLocation()">
                    <option value="">Select District</option>
                    <?php foreach ($districts as $district => $barangays): ?>
                        <option value="<?= htmlspecialchars($district) ?>"><?= htmlspecialchars($district) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="barangay" id="barangay" class="form-control" required disabled onchange="updateLocation()">
                    <option value="">Select Barangay</option>
                </select>
                <input type="hidden" name="location" id="location">
            </div>
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <div class="contact-input-wrapper">
                <span class="prefix">+639</span>
                <input type="text" class="form-control" name="phone" id="contact" maxlength="9" required>
            </div>
            <small class="form-text text-muted">Enter 9 digits after +639</small>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="appointment_date">Appointment Date</label>
            <input type="date" class="form-control" name="appointment_date" id="appointment_date" value="<?= htmlspecialchars($appointment_date) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="appointment_time">Appointment Time</label>
            <input type="text" class="form-control" name="appointment_time" id="appointment_time" value="<?= htmlspecialchars($appointment_time) ?>" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="service">Select Service</label>
            <select class="form-control" name="service" id="service" required>
                <option value="">Select Service</option>
                <?php foreach ($services_prices as $service => $price): ?>
                    <option value="<?= htmlspecialchars($service) ?>"><?= htmlspecialchars($service) ?> - PHP <?= number_format($price) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="service_price">Service Price (PHP)</label>
            <input type="text" class="form-control" id="service_price" name="service_price" readonly>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="note">Additional Notes</label>
            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Add any special instructions or additional information here"></textarea>
        </div>
    </div>

    <div class="modal-actions">
        <button type="button" class="btn btn-secondary" onclick="showTimeSlots('<?= htmlspecialchars($appointment_date) ?>')">
            <i class="fas fa-arrow-left me-2"></i>Back to Time Slots
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-calendar-check me-2"></i>Confirm Booking
        </button>
    </div>
</form> 