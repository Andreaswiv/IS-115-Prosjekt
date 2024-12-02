document.addEventListener("DOMContentLoaded", () => {
    const roomTypeSelect = document.getElementById("room_type");
    const roomImage = document.getElementById("room_image");
    const roomImageContainer = document.getElementById("room_image_container");

    // Map room types to their images
    const roomImages = {
        "Single Room": "../../public/assets/img/single_room.JPG",
        "Double Room": "../../public/assets/img/double_room.jpg",
        "King Suite": "../../public/assets/img/king_suite.jpeg"
    };

    // Update the room image when the room type changes
    const updateRoomImage = () => {
        const selectedRoomType = roomTypeSelect.value;
        if (roomImages[selectedRoomType]) {
            roomImage.src = roomImages[selectedRoomType];
            roomImage.style.display = "block"; // Show the image
        } else {
            roomImage.style.display = "none"; // Hide the image if no match
        }
    };

    // Initial image update based on preselected room type
    updateRoomImage();

    // Add event listener for room type changes
    roomTypeSelect.addEventListener("change", updateRoomImage);
});