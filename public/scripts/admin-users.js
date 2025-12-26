// Run script after the page is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Listen for all form submissions using event delegation
    document.body.addEventListener("submit", (e) => {
        const form = e.target;

        // Ask for confirmation before deleting a user
        const ok = window.confirm("Are you sure you want to delete this user?");

        // Cancel form submission if admin clicks "Cancel"
        if (!ok) {
            e.preventDefault();
        }
    });
});