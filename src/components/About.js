// path: src/pages/About.js
import "./About.css";

/* 
  דף "About" אינפורמטיבי:
  - תוכן קריא למרצה/בודק.
  - בלי תלות ב־API.
  - אפשר להרחיב/לקצר בקלות.
*/
export default function About(){
  return (
    <div className="about-wrap">
      <h1>About TripMaster</h1>
      <p className="lead">
        TripMaster helps you plan trips, keep a clean history of finished itineraries, share your story with the community, and clone inspirational trips into your own calendar.
      </p>

      <h2>What you can do</h2>
      <ul>
        <li><b>Plan:</b> build day-by-day itineraries with timings and places.</li>
        <li><b>History:</b> keep finished trips; rate them, add notes &amp; photos, then share.</li>
        <li><b>Share your Story:</b> browse others’ trips, view details &amp; photos, and clone.</li>
        <li><b>Bulletin Board:</b> post quick notices during your trip.</li>
        <li><b>Find a Partner:</b> look for travel partners with similar plans.</li>
      </ul>

      <h2>How sharing works</h2>
      <ol>
        <li>Open <b>History</b> → pick a finished trip → add rating, notes, and optional photos.</li>
        <li>Click <b>Done</b> to publish it. It moves to <b>Shared/Archived</b>.</li>
        <li>Go to <b>Share your Story</b> to see it live. Others can view or clone it.</li>
      </ol>

      <h2>Privacy</h2>
      <p>We only publish what you explicitly share. You can edit or remove shared stories at any time. Media uploads are stored on our server and linked to your user ID.</p>

      <h2>Contact</h2>
      <p>Feedback or issues? Email us at <a href="mailto:support@tripmaster.app">support@tripmaster.app</a>.</p>
    </div>
  );
}
