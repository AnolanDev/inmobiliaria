import { Injectable } from '@nestjs/common';
import { join } from 'path';
import { mkdirSync, writeFileSync } from 'fs';
import { v4 as uuidv4 } from 'uuid';

@Injectable()
export class UploadService {
  private uploadDir = join(process.cwd(), 'uploads'); // ✅ Directorio raíz

  constructor() {
    mkdirSync(this.uploadDir, { recursive: true });
  }

  async uploadLogo(fileBuffer: Buffer, originalName: string, projectId: number): Promise<string> {
    const folder = join(this.uploadDir, 'projects', projectId.toString());
    mkdirSync(folder, { recursive: true });

    const ext = originalName.split('.').pop();
    const filename = `logo.${ext}`;

    const path = join(folder, filename);
    writeFileSync(path, fileBuffer);

    return `/uploads/projects/${projectId}/${filename}`; // ✅ URL pública
  }

  async uploadGallery(files: { buffer: Buffer; originalname: string }[], projectId: number): Promise<string[]> {
    const folder = join(this.uploadDir, 'gallery', projectId.toString());
    mkdirSync(folder, { recursive: true });

    const urls: string[] = [];

    for (const file of files) {
      const ext = file.originalname.split('.').pop();
      const filename = `${uuidv4()}.${ext}`;
      const path = join(folder, filename);

      writeFileSync(path, file.buffer);
      urls.push(`/uploads/gallery/${projectId}/${filename}`);
    }

    return urls;
  }
}
