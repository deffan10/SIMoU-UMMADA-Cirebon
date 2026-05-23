# Entity Relationship Diagram (ERD)

## Diagram Relasi

```
┌─────────────┐       ┌─────────────────┐       ┌──────────────┐
│   admins    │       │   institutions  │       │  categories  │
├─────────────┤       ├─────────────────┤       ├──────────────┤
│ id (PK)     │       │ id (PK)         │       │ id (PK)      │
│ name        │       │ name            │       │ name         │
│ email       │       │ slug            │       │ slug         │
│ password    │       │ logo            │       │ color        │
│ photo       │       │ type            │       │ icon         │
│ phone       │       │ country         │       │ description  │
│ is_active   │       │ city            │       │ is_active    │
│ last_login  │       │ website         │       │ sort_order   │
│ timestamps  │       │ email/phone     │       │ timestamps   │
└──────┬──────┘       │ address         │       └──────┬───────┘
       │              │ description     │              │
       │              │ is_active       │              │
       │              │ timestamps      │              │
       │              │ soft_deletes    │              │
       │              └────────┬────────┘              │
       │                       │                       │
       │         ┌─────────────┼───────────────────────┘
       │         │             │
       │         ▼             ▼
       │    ┌──────────────────────────────────────┐
       └───▶│              mous                     │
            ├──────────────────────────────────────┤
            │ id (PK)                              │
            │ mou_number (unique)                  │
            │ title                                │
            │ slug (unique)                        │
            │ institution_id (FK → institutions)   │
            │ category_id (FK → categories)        │
            │ level (lokal/nasional/internasional)  │
            │ type (akademik/penelitian/...)        │
            │ cooperation_type (mou/moa/ia/pks)    │
            │ faculty_id (FK → faculties)           │
            │ study_program                        │
            │ pic_name/phone/email                 │
            │ start_date                           │
            │ end_date                             │
            │ duration_months                      │
            │ status (aktif/akan_expire/expire)    │
            │ visibility (public/internal)         │
            │ description                          │
            │ public_summary                       │
            │ main_document                        │
            │ show_pdf_public                      │
            │ allow_download                       │
            │ renewal_count                        │
            │ created_by (FK → admins)             │
            │ updated_by (FK → admins)             │
            │ timestamps + soft_deletes            │
            └──────────┬───────────────────────────┘
                       │
          ┌────────────┼────────────────┐
          │            │                │
          ▼            ▼                ▼
┌──────────────┐ ┌──────────────┐ ┌──────────────────┐
│ mou_renewals │ │ attachments  │ │  notifications   │
├──────────────┤ ├──────────────┤ ├──────────────────┤
│ id (PK)      │ │ id (PK)      │ │ id (PK)          │
│ mou_id (FK)  │ │ mou_id (FK)  │ │ mou_id (FK)      │
│ renewal_num  │ │ original_name│ │ type             │
│ old_start    │ │ file_path    │ │ title            │
│ old_end      │ │ file_type    │ │ message          │
│ new_start    │ │ file_size    │ │ is_read          │
│ new_end      │ │ mime_type    │ │ read_at          │
│ duration     │ │ version      │ │ timestamps       │
│ renewal_note │ │ description  │ └──────────────────┘
│ old_file     │ │ uploaded_by  │
│ new_file     │ │ timestamps   │
│ renewed_by   │ │ soft_deletes │
│ timestamps   │ └──────────────┘
└──────────────┘

┌─────────────┐     ┌──────────────────┐
│  faculties  │     │  study_programs  │
├─────────────┤     ├──────────────────┤
│ id (PK)     │◄────│ faculty_id (FK)  │
│ name        │     │ id (PK)          │
│ slug        │     │ name             │
│ code        │     │ slug             │
│ description │     │ code             │
│ is_active   │     │ level            │
│ timestamps  │     │ is_active        │
└─────────────┘     │ timestamps       │
                    └──────────────────┘

┌──────────────────┐     ┌──────────────────┐
│  activity_logs   │     │   import_logs    │
├──────────────────┤     ├──────────────────┤
│ id (PK)          │     │ id (PK)          │
│ admin_id (FK)    │     │ admin_id (FK)    │
│ action           │     │ file_name        │
│ model_type       │     │ file_path        │
│ model_id         │     │ total_rows       │
│ description      │     │ success_count    │
│ old_values (JSON)│     │ failed_count     │
│ new_values (JSON)│     │ duplicate_count  │
│ ip_address       │     │ status           │
│ user_agent       │     │ errors (JSON)    │
│ timestamps       │     │ summary (JSON)   │
└──────────────────┘     │ timestamps       │
                         └──────────────────┘
```

## Relasi Utama

| From | To | Type | Description |
|------|-----|------|-------------|
| mous | institutions | Many-to-One | Setiap MoU milik satu institusi |
| mous | categories | Many-to-One | Setiap MoU memiliki satu kategori |
| mous | faculties | Many-to-One | MoU dapat terkait fakultas |
| mous | admins | Many-to-One | Created/Updated by admin |
| mou_renewals | mous | Many-to-One | Histori perpanjangan MoU |
| mou_renewals | admins | Many-to-One | Diperpanjang oleh admin |
| attachments | mous | Many-to-One | File lampiran MoU |
| notifications | mous | Many-to-One | Notifikasi terkait MoU |
| study_programs | faculties | Many-to-One | Prodi milik fakultas |
| activity_logs | admins | Many-to-One | Log aktivitas admin |
| import_logs | admins | Many-to-One | Log import oleh admin |
