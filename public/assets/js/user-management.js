document.addEventListener("DOMContentLoaded", function () {
    console.log("JavaScript loaded");

    // Handle "viewType" dropdown change
    const viewTypeDropdown = document.getElementById("viewType");
    if (viewTypeDropdown) {
        viewTypeDropdown.addEventListener("change", function () {
            const form = this.closest("form");
            if (form) {
                console.log("Submitting form for viewType change");
                form.submit();
            } else {
                console.error("Form not found for viewType dropdown");
            }
        });
    }

    // Handle "groupBy" dropdown change
    const groupByDropdown = document.getElementById("groupBy");
    if (groupByDropdown) {
        groupByDropdown.addEventListener("change", function () {
            const form = this.closest("form");
            if (form) {
                console.log("Submitting form for groupBy change");
                form.submit();
            } else {
                console.error("Form not found for groupBy dropdown");
            }
        });
    }

    // Handle toggleSortOrder functionality
    window.toggleSortOrder = function () {
        const { orderDir, viewType, groupBy } = phpVars;
        const newOrder = orderDir === 'ASC' ? 'DESC' : 'ASC';
        window.location.href = `?sortBy=registrationDate&order=${newOrder}&view=${viewType}&groupBy=${groupBy}`;
    };

    // Confirm role change
    window.confirmRoleChange = function () {
        const roleDropdown = document.getElementById('role');
        const { originalRole } = phpVars;
        const selectedRole = roleDropdown.value;

        if (selectedRole !== originalRole) {
            return confirm("Du holder på å bytte rolle på denne brukeren, sikker på at du vil gjøre det?");
        }
        return true;
    };
});
