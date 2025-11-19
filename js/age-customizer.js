document.addEventListener('DOMContentLoaded', function() {

    // 1. Get the user's age from the <body> tag in your HTML.
    const body = document.body;
    const userAge = parseInt(body.dataset.userAge, 10);

    console.log('Age found on homepage:', userAge); // <-- ADD THIS

    // Make sure we successfully got an age. If not, stop the script.
    if (isNaN(userAge)) {
        console.error("Could not find user age. Make sure it's in the body tag, like <body data-user-age='4'>");
        return;
    }

    // 2. Define the different URLs for each age group.
    let bookshelfUrl;
    let computerUrl;
    let whiteboardUrl; // Add variables for all your sections
    let radioUrl; 
    let toyboxUrl  // Example for another section

    if (userAge <= 3) {
        // Content for kids aged 2-3
        bookshelfUrl = 'section/storyland/storybooks_list.php';
        computerUrl  = 'section/brainplay/games2_3.php';
        whiteboardUrl  = 'section/colorfun/2-3 years/draw2_3.php';
        radioUrl     = 'section/TuneTown/music2_3.php';
        toyboxUrl    = 'section/letterbeats/abc1.php';
    } else if (userAge === 4) {
        // Content for 4-year-olds
       bookshelfUrl = 'section/storyland/storybooks_list.php';
        computerUrl  = 'section/brainplay/games4.php';
        whiteboardUrl  = 'section/colorfun/4-5 years/draw4_5.php';
        radioUrl     = 'section/TuneTown/music4.php';
        toyboxUrl    = 'section/letterbeats/abc2.php';
    } else if (userAge === 5) {
        // Content for 5-year-olds
       bookshelfUrl = 'section/storyland/storybooks_list.php';
        computerUrl  = 'section/brainplay/games5.php';
        whiteboardUrl  = 'section/colorfun/4-5 years/draw4_5.php';
        radioUrl     = 'section/TuneTown/music5.php';
        toyboxUrl    = 'section/letterbeats/abc2.php';
    }

    // 3. Find the links (hotspots) on the page and update their 'href' attribute.
    // IMPORTANT: Your HTML links must have these exact IDs for this to work.
    const bookshelfLink = document.getElementById('bookshelf-link');
    const computerLink = document.getElementById('computer-link');
    const whiteboardLink = document.getElementById('whiteboard-link');
    const radioLink = document.getElementById('radio-link');
    const toyboxLink = document.getElementById('toybox-link');

    // Update the href for each link if it exists on the page.
    if (bookshelfLink) bookshelfLink.href = bookshelfUrl;
    if (computerLink) computerLink.href = computerUrl;
    if (whiteboardLink) whiteboardLink.href = whiteboardUrl;
    if (radioLink) radioLink.href = radioUrl;
     if (toyboxLink) toyboxLink.href = toyboxUrl;
});