<?php
namespace App\Controllers\Admin;

use App\Repositories\EmailTemplateRepository;
use App\Utils\Auth;
use App\Utils\CSRF;
use App\Utils\Validator;

class EmailTemplateController
{
    protected $repo;

    public function __construct(EmailTemplateRepository $repository)
    {
        Auth::requireLogin();
        $this->repo = $repository;
    }

    public function index(): void
    {
        $templates = $this->repo->getAll();
        $csrfField = CSRF::inputField();
        include __DIR__ . '/../../views/admin/email_templates.php';
    }

    public function showCreate(): void
    {
        Auth::requireLogin();
        $csrfField = CSRF::inputField();
        include __DIR__ . '/../../views/admin/email_template_form.php';
    }

    public function create(): void
    {
        Auth::requireLogin();

        $post = $_POST;

        // CSRF check
        if (!CSRF::validate($post['csrf_token'] ?? null)) {
            $_SESSION['template_error'] = 'Invalid session token.';
            header('Location: /admin/email-templates/create');
            exit;
        }

        // Validate
        $name = trim($post['name'] ?? '');
        $subject = trim($post['subject'] ?? '');
        $body = trim($post['body'] ?? '');

        if (empty($name) || empty($subject) || empty($body)) {
            $_SESSION['template_error'] = 'All fields are required.';
            header('Location: /admin/email-templates/create');
            exit;
        }

        // Create
        $ok = $this->repo->create([
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
        ]);

        if ($ok) {
            $_SESSION['template_success'] = 'Email template created successfully.';
            header('Location: /admin/email-templates');
        } else {
            $_SESSION['template_error'] = 'Failed to create email template.';
            header('Location: /admin/email-templates/create');
        }
        exit;
    }

    public function showEdit(int $id): void
    {
        Auth::requireLogin();
        $template = $this->repo->findById($id);
        if (!$template) {
            header('Location: /admin/email-templates');
            exit;
        }
        $csrfField = CSRF::inputField();
        include __DIR__ . '/../../views/admin/email_template_form.php';
    }

    public function update(int $id): void
    {
        Auth::requireLogin();

        $template = $this->repo->findById($id);
        if (!$template) {
            header('Location: /admin/email-templates');
            exit;
        }

        $post = $_POST;

        // CSRF check
        if (!CSRF::validate($post['csrf_token'] ?? null)) {
            $_SESSION['template_error'] = 'Invalid session token.';
            header("Location: /admin/email-templates/edit/{$id}");
            exit;
        }

        // Validate
        $name = trim($post['name'] ?? '');
        $subject = trim($post['subject'] ?? '');
        $body = trim($post['body'] ?? '');

        if (empty($name) || empty($subject) || empty($body)) {
            $_SESSION['template_error'] = 'All fields are required.';
            header("Location: /admin/email-templates/edit/{$id}");
            exit;
        }

        // Update
        $ok = $this->repo->update($id, [
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
        ]);

        if ($ok) {
            $_SESSION['template_success'] = 'Email template updated successfully.';
            header('Location: /admin/email-templates');
        } else {
            $_SESSION['template_error'] = 'Failed to update email template.';
            header("Location: /admin/email-templates/edit/{$id}");
        }
        exit;
    }

    public function delete(int $id): void
    {
        Auth::requireLogin();

        $template = $this->repo->findById($id);
        if (!$template) {
            header('Location: /admin/email-templates');
            exit;
        }

        $post = $_POST;

        // CSRF check
        if (!CSRF::validate($post['csrf_token'] ?? null)) {
            $_SESSION['template_error'] = 'Invalid session token.';
            header('Location: /admin/email-templates');
            exit;
        }

        $ok = $this->repo->delete($id);

        if ($ok) {
            $_SESSION['template_success'] = 'Email template deleted successfully.';
        } else {
            $_SESSION['template_error'] = 'Failed to delete email template.';
        }

        header('Location: /admin/email-templates');
        exit;
    }

    public function showHistory(int $templateId): void
    {
        Auth::requireLogin();
        $template = $this->repo->findById($templateId);
        if (!$template) {
            $_SESSION['template_error'] = 'Template not found.';
            header('Location: /admin/email-templates');
            exit;
        }

        $versions = $this->repo->findVersionsByTemplateId($templateId);
        $csrfField = CSRF::inputField();

        include __DIR__ . '/../../views/admin/email_template_history.php';
    }

    public function restore(int $versionId): void
    {
        Auth::requireLogin();

        // CSRF check
        if (!CSRF::validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['template_error'] = 'Invalid session token.';
            // We don't know the parent template ID here, so redirect to the main list
            header('Location: /admin/email-templates');
            exit;
        }

        $version = $this->repo->findVersionById($versionId);

        if (!$version) {
            $_SESSION['template_error'] = 'Version not found.';
            header('Location: /admin/email-templates');
            exit;
        }

        // Prepare data from the old version to update the main template
        $currentTemplate = $this->repo->findById($version['template_id']);
        $dataToRestore = [
            'name' => $currentTemplate['name'], // Keep current name
            'subject' => $version['subject'],
            'body' => $version['body'],
        ];

        if ($this->repo->update($version['template_id'], $dataToRestore)) {
            $_SESSION['template_success'] = 'Template successfully restored from a previous version.';
        } else {
            $_SESSION['template_error'] = 'Failed to restore template.';
        }

        header('Location: /admin/email-templates/edit/' . $version['template_id']);
        exit;
    }
}