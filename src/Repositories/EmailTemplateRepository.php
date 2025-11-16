<?php

namespace App\Repositories;

class EmailTemplateRepository
{
    private ?\PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Finds a single email template by its unique name.
     *
     * @param string $name The unique name of the template (e.g., 'booking_confirmation').
     * @return array|null The template data as an associative array, or null if not found.
     */
    public function findByName(string $name): ?array
    {
        if (!$this->db) {
            error_log("EmailTemplateRepository findByName Error: PDO not available");
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE name = :name LIMIT 1");
            $stmt->execute(['name' => $name]);
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $template ?: null;
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository findByName Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetches all email templates from the database.
     *
     * @return array An array of all templates.
     */
    public function getAll(): array
    {
        if (!$this->db) {
            error_log("EmailTemplateRepository getAll Error: PDO not available");
            return [];
        }

        try {
            $stmt = $this->db->query("SELECT * FROM email_templates ORDER BY name ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository getAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Finds a single email template by its ID.
     *
     * @param int $id The ID of the template.
     * @return array|null The template data or null if not found.
     */
    public function findById(int $id): ?array
    {
        if (!$this->db) {
            return null;
        }
        try {
            $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $template ?: null;
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository findById Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Creates a new email template.
     *
     * @param array $data The data for the new template.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        if (!$this->db) {
            return false;
        }
        try {
            $sql = "INSERT INTO email_templates (name, subject, body, created_at, updated_at) VALUES (:name, :subject, :body, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'name' => $data['name'],
                'subject' => $data['subject'],
                'body' => $data['body'],
            ]);
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing email template.
     *
     * @param int $id The ID of the template to update.
     * @param array $data The new data for the template.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // 1. Get the current state of the template before updating
            $currentTemplate = $this->findById($id);
            if (!$currentTemplate) {
                $this->db->rollBack();
                return false; // Template not found
            }

            // 2. Archive the current version
            $archiveSql = "INSERT INTO email_template_versions (template_id, subject, body, created_at) VALUES (:template_id, :subject, :body, :created_at)";
            $archiveStmt = $this->db->prepare($archiveSql);
            $archiveStmt->execute([
                'template_id' => $id,
                'subject' => $currentTemplate['subject'],
                'body' => $currentTemplate['body'],
                'created_at' => $currentTemplate['updated_at'] // Use the last update time as the creation time for the version
            ]);

            // 3. Update the main template record with the new data
            $sql = "UPDATE email_templates SET name = :name, subject = :subject, body = :body, updated_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $data['id'] = $id;
            $stmt->execute($data);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("EmailTemplateRepository update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an email template by its ID.
     *
     * @param int $id The ID of the template to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        if (!$this->db) {
            return false;
        }
        try {
            $stmt = $this->db->prepare("DELETE FROM email_templates WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds all historical versions for a given template ID.
     *
     * @param int $templateId The ID of the parent template.
     * @return array An array of version records.
     */
    public function findVersionsByTemplateId(int $templateId): array
    {
        if (!$this->db) {
            return [];
        }
        try {
            $stmt = $this->db->prepare("SELECT * FROM email_template_versions WHERE template_id = :template_id ORDER BY created_at DESC");
            $stmt->execute(['template_id' => $templateId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository findVersionsByTemplateId Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Finds a single template version by its own ID.
     *
     * @param int $versionId The ID of the version record.
     * @return array|null The version data or null if not found.
     */
    public function findVersionById(int $versionId): ?array
    {
        if (!$this->db) {
            return null;
        }
        try {
            $stmt = $this->db->prepare("SELECT * FROM email_template_versions WHERE id = :id");
            $stmt->execute(['id' => $versionId]);
            $version = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $version ?: null;
        } catch (\PDOException $e) {
            error_log("EmailTemplateRepository findVersionById Error: " . $e->getMessage());
            return null;
        }
    }
}