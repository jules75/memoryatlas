

ABOUT

Memory Atlas is a wiki-like site that allows users to create their own text entries, then markup that text with location, image, date or video information (plus some other types). These entries are designed to be very simple to edit and markup, with little technical knowledge required.

The data from these pages is then extracted and displayed in other ways, e.g. on a map, timeline etc.


ARCHITECTURE

PHP 7 drives the API and serves the web pages. The API is called via the /api/v1 URL (more details API documentation coming).

The Quill javascript library is used to mark up the text when users edit entries. Whenever a logged-in user makes a change to an entry, the entire Quill object is dumped to MongoDB. This way there is a full history of every change made to each entry.

User accounts are also stored in MongoDB. Passwords are hashed and salted using PHP's password_hash() function.

With the exception of user accounts, Memory Atlas favours an architecture where all data is visible, if not necessarily editable.

Javascript source files are 'compiled' to a single file (entry.compiled.js) using Babel. See the /scripts/local/startup.sh file for syntax.


INDEXING

The 'entries' MongoDB database is regularly searched for markup data, and JSON files are created. These files can be found in the /cache directory. This is done to avoid inefficient searching of the database. Currently, these index files are updated on the server every 1 minute via a cron job.


CLOUDINARY

Rather than deal with the storage and processing complications involved with image uploads, all images are sent to Cloudinary's image servers. Their APIs make it easy to rescale and crop images, as well as many other features. Cloudinary credentials are stored in config.php.

