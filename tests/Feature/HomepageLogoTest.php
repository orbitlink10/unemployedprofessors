<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HomepageLogoTest extends TestCase
{
    private string $testStorage;
    private array $uploadedPaths = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->testStorage = sys_get_temp_dir().'/homepage-logo-'.bin2hex(random_bytes(8));
        mkdir($this->testStorage.'/app', 0755, true);
        $this->app->useStoragePath($this->testStorage);
    }

    protected function tearDown(): void
    {
        foreach ($this->uploadedPaths as $path) {
            if (is_file(public_path($path))) {
                unlink(public_path($path));
            }
        }
        if (is_file($this->testStorage.'/app/homepage.json')) {
            unlink($this->testStorage.'/app/homepage.json');
        }
        rmdir($this->testStorage.'/app');
        rmdir($this->testStorage);
        parent::tearDown();
    }

    private function content(): array
    {
        $content = defaultHomepageContent();
        $data = ['seo_html' => $content['seo_html']];
        foreach ($content['hero'] as $key => $value) {
            $data['hero_'.$key] = $value;
        }
        foreach ($content['badges'] as $index => $badge) {
            foreach ($badge as $key => $value) {
                $data['badge_'.$key.'_'.($index + 1)] = $value;
            }
        }
        foreach ($content['cards'] as $index => $card) {
            foreach ($card as $key => $value) {
                $data['card_'.$key.'_'.($index + 1)] = $value;
            }
        }
        return $data;
    }

    public function test_admin_can_upload_preserve_replace_and_remove_logo(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
        $this->withSession(['admin_logged_in' => true]);
        $this->post('/admin/homepage', $this->content() + [
            'logo' => UploadedFile::fake()->createWithContent('logo.png', $png),
        ])->assertSessionHasNoErrors()->assertRedirect();
        $first = loadHomepageContent()['logo_path'];
        $this->uploadedPaths[] = $first;
        $this->assertFileExists(public_path($first));
        $this->get('/')->assertOk()->assertSee(asset($first));
        $this->get('/admin/homepage')->assertOk()->assertSee('Upload or replace logo');

        $this->post('/admin/homepage', $this->content())->assertSessionHasNoErrors();
        $this->assertSame($first, loadHomepageContent()['logo_path']);

        $this->post('/admin/homepage', $this->content() + [
            'logo' => UploadedFile::fake()->createWithContent('replacement.png', $png),
        ])->assertSessionHasNoErrors();
        $second = loadHomepageContent()['logo_path'];
        $this->uploadedPaths[] = $second;
        $this->assertNotSame($first, $second);
        $this->assertFileDoesNotExist(public_path($first));
        $this->assertFileExists(public_path($second));

        $this->post('/admin/homepage', $this->content() + ['remove_logo' => '1'])
            ->assertSessionHasNoErrors();
        $this->assertNull(loadHomepageContent()['logo_path']);
        $this->assertFileDoesNotExist(public_path($second));
        $this->get('/')->assertSee('class="mark"', false);
    }

    public function test_upload_requires_admin_and_rejects_non_images(): void
    {
        $this->post('/admin/homepage', $this->content())
            ->assertRedirect(route('admin.login'));
        $this->withSession(['admin_logged_in' => true])->post('/admin/homepage', $this->content() + [
            'logo' => UploadedFile::fake()->createWithContent('logo.php', '<?php echo 1;'),
        ])->assertSessionHasErrors('logo');
        $this->assertNull(loadHomepageContent()['logo_path']);
    }
}
