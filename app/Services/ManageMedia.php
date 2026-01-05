<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class ManageMedia
{
	public static function storeCommentMedia(string $discussion_slug, string $comment_slug, array $media = []): array
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

	public static function updateMedia(string $path, array $media, array $media_to_rmeove): array
	{
		if (sizeof($media_to_rmeove) === 0) {
			return $media;
		}

		self::deleteMedia($path, $media_to_rmeove);

		return array_values(array_diff($media, $media_to_rmeove));
	}

	public static function deleteMedia(string $path, array $media): void
	{
		foreach ($media as $file) {
			self::deleteFile("{$path}/{$file}");
		}
	}

	public static function deleteFile(string $path): void
	{
		if (Storage::fileExists($path)) {
			Storage::delete($path);
		}
	}

	public static function deleteDirectoryWithMedia(string $path, array $media): void
	{
		if (!Storage::directoryExists($path)) {
			return;
		}

		self::deleteMedia($path, $media);
		Storage::deleteDirectory($path);
	}

	public static function replaceFile(string $path, string $existing_file, UploadedFile $file, string $name): void
	{
		$temp_path = "{$path}/{$existing_file}";

		if (Storage::fileExists($temp_path)) {
			self::deleteFile($temp_path);
		}

		Storage::putFileAs(
			$path,
			$file,
			$name
		);
	}
}
