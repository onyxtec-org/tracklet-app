@extends('layouts.contentLayoutMaster')

@section('title', 'Add Device')

@section('content')
@include('panels.response')

<div class="card">
    <div class="card-body">
        <form action="{{ route('device.store') }}" method="POST" id="deviceForm">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Device Name</label>
                <input type="text" name="device_name" value="{{ old('device_name') }}" class="form-control" required>
                @error('device_name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div id="versions-container">
                <h5 class="fw-bold">Device Versions</h5>
                <button type="button" class="btn btn-primary btn-sm mb-2" id="add-version">Add More Versions</button>
            </div>

            <button type="submit" class="btn btn-success mt-3">Submit Device</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const versionContainer = document.getElementById("versions-container");
    const addVersionBtn = document.getElementById("add-version");
    let versionIndex = 0; // Track version index

    function addVersion() {
        let versionCard = document.createElement("div");
        versionCard.classList.add("card", "p-3", "mb-3", "border", "border-secondary");
        versionCard.dataset.index = versionIndex; // Assign index to track versions

        versionCard.innerHTML = `
            <div class="d-flex justify-content-between">
                <h6 class="fw-bold">Version</h6>
                <button type="button" class="btn btn-danger btn-sm remove-version">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Version Name</label>
                <input type="text" class="form-control" name="versions[${versionIndex}][version_name]" required>
            </div>

            <div class="color-section">
                <h6 class="fw-bold text-primary">Colors</h6>
                <button type="button" class="btn btn-sm btn-success add-color" data-version="${versionIndex}">Add Color</button>
                <div class="colors-container mt-2"></div>
            </div>
        `;

        // Remove version
        versionCard.querySelector(".remove-version").addEventListener("click", function () {
            versionCard.remove();
        });

        // Handle Colors
        versionCard.querySelector(".add-color").addEventListener("click", function (event) {
            let versionId = event.target.dataset.version;
            let colorInput = document.createElement("input");
            colorInput.type = "text";
            colorInput.name = `versions[${versionId}][colors][]`;  // Now colors are inside the same version
            colorInput.classList.add("form-control", "mb-2");
            colorInput.placeholder = "Color";
            // colorInput.required = true;
            versionCard.querySelector(".colors-container").appendChild(colorInput);
        });

        versionContainer.appendChild(versionCard);
        versionIndex++; // Increment index
    }

    // Ensure at least one version is added on page load
    addVersion();

    // Add new version on button click
    addVersionBtn.addEventListener("click", function () {
        addVersion();
    });

    // Prevent form submission if no versions are added
    document.querySelector("#deviceForm").addEventListener("submit", function (event) {
        if (versionContainer.querySelectorAll(".card").length === 0) {
            alert("You must add at least one device version.");
            event.preventDefault();
        }
    });
});
</script>

@endsection