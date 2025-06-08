*/cloudinary_file_manager/
├── cloudinary_php/              ← from your local Composer
│   └── vendor/...
├── db.php                       ← database connection
├── cloudinary_config.php        ← SDK config
├── index.html                   ← frontend
├── upload.php                   ← upload + save to DB
├── fetch_files.php              ← fetch from DB
├── delete_file.php              ← delete from Cloudinary + DB
├── update_file.php              ← update metadata in Cloudinary + DB*
