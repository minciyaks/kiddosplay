/**
 * Activity Logger Script v2
 *
 * This script sends "enter" and "leave" events to a single PHP endpoint.
 * It relies on a global variable `currentSection` being defined in the HTML page.
 */
(function() {
    // Check if the currentSection variable has been set on the page.
    if (typeof currentSection === 'undefined') {
        console.error("Error: 'currentSection' is not defined. Please define it before loading this script.");
        return; // Stop the script if the section name isn't set.
    }

    async function logActivity(eventType, sectionName, logId) {
        let data = new FormData();
        data.append('eventType', eventType);
        data.append('sectionName', sectionName);
        if (logId) {
            data.append('log_id', logId);
        }

        try {
            // *** THIS PATH MUST START WITH /project/ ***
            const response = await fetch('/project/api/log_activity.php', {
                method: 'POST',
                body: data
            });
            return await response.json();
        } catch (error) {
            console.error('Fetch Error:', error);
            return { status: 'error', message: 'Fetch failed' };
        }
    }

    // 1. Tell the server that the user has started viewing this section ("enter").
    logActivity('enter', currentSection, null)
        .then(result => {
            if (result.status === 'success' && result.log_id) {
                sessionStorage.setItem('currentLogId', result.log_id);
                console.log(`Started logging section: ${currentSection} with log_id: ${result.log_id}`);
            } else {
                console.error('Logging Error:', result.message);
            }
        });


    // 2. Tell the server when the user leaves the page ("leave").
    window.addEventListener('beforeunload', function() {
        const logIdToUpdate = sessionStorage.getItem('currentLogId');

        if (logIdToUpdate) {
            let data = new FormData();
            data.append('eventType', 'leave');
            data.append('sectionName', currentSection);
            data.append('log_id', logIdToUpdate);
            
            // *** THIS PATH MUST ALSO START WITH /project/ ***
            navigator.sendBeacon('/project/api/log_activity.php', data);
            
            sessionStorage.removeItem('currentLogId');
        }
    });
})();