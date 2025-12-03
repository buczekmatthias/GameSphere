<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

final class StoreCommentMedia
{
	public static function storeFiles(string $discussion_slug, string $comment_slug, array $media = []): array
	{
		$path = "discussions/{$discussion_slug}/{$comment_slug}";
		if (!Storage::directoryExists($path)) {
			Storage::makeDirectory($path);
		}

		$tempMedia = [];

		if (count($media) > 0) {
			foreach ($media as $i => $file) {
				$fileName = "{$comment_slug}-media-{$i}.{$file->extension()}";
				Storage::putFileAs($path, $file, $fileName);
				$tempMedia[] = $fileName;
			}
		}

		return $tempMedia;
	}
}
