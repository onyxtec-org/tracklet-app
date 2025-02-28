@extends('layouts.contentLayoutMaster')

@section('title', 'Add Shipping Address')

@section('content')
@include('panels.response')

<div class="card">
    <div class="card-body">
        <form action="{{ route('shipping.store') }}" method="POST" id="shippingForm">
            @csrf
            <div id="addresses-container">
                <button type="button" class="btn btn-primary btn-sm mb-2" id="add-address">Add More Addresses</button>
            </div>

            <button type="submit" class="btn btn-success mt-3">Submit Addresses</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const addressContainer = document.getElementById("addresses-container");
    const addAddressBtn = document.getElementById("add-address");
    let addressIndex = 0;

    function addAddress(addressValue = '') {
        let addressCard = document.createElement("div");
        addressCard.classList.add("card", "p-3", "mb-3", "border", "border-secondary");
        addressCard.dataset.index = addressIndex;

        addressCard.innerHTML = `
            <div class="d-flex justify-content-between">
                <h6 class="fw-bold">Address</h6>
                <button type="button" class="btn btn-danger btn-sm remove-address">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="addresses[]" value="${addressValue}" required>
            </div>
        `;

        // Remove address button functionality
        addressCard.querySelector(".remove-address").addEventListener("click", function () {
            addressCard.remove();
        });

        addressContainer.appendChild(addressCard);
        addressIndex++;
    }

    // Load previous input values if validation fails
    @if(old('addresses'))
        @foreach(old('addresses') as $index => $address)
            addAddress("{{ $address }}");
        @endforeach
    @else
        addAddress();
    @endif

    addAddressBtn.addEventListener("click", function () {
        addAddress();
    });

    document.querySelector("#shippingForm").addEventListener("submit", function (event) {
        if (addressContainer.querySelectorAll(".card").length === 0) {
            alert("You must add at least one shipping address.");
            event.preventDefault();
        }
    });
});
</script>

@endsection
