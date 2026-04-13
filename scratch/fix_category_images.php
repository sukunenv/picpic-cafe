<?php
$count = DB::table('categories')->where('image', 'like', '%127.0.0.1%')->update(['image' => null]);
echo "Updated $count categories with local image URLs to null.\n";
