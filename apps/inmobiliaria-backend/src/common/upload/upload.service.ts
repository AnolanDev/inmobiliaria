import { Injectable } from '@nestjs/common';
import { join } from 'path';
import { mkdirSync, writeFileSync } from 'fs';
import { v4 as uuidv4 } from 'uuid';

@Injectable()
export class UploadService {
  private uploadDir = join(process.cwd(), 'uploads');

  constructor() {
    mkdirSync(this.uploadDir, { recursive: true });
  }

  async uploadLogo(
    fileBuffer: Buffer,
    originalName: string,
    projectId: number,
  ): Promise<string> {
    const folder = join(this.uploadDir, 'projects', projectId.toString());
    mkdirSync(folder, { recursive: true });

    const ext = originalName.split('.').pop();
    const filename = `logo.${ext}`;
    const fullPath = join(folder, filename);

    writeFileSync(fullPath, fileBuffer);
    return `/uploads/projects/${projectId}/${filename}`;
  }

  async uploadGallery(
    files: { buffer: Buffer; originalname: string }[],
    projectId: number,
  ): Promise<string[]> {
    const folder = join(this.uploadDir, 'gallery', projectId.toString());
    mkdirSync(folder, { recursive: true });

    const urls: string[] = [];
    for (const file of files) {
      const ext = file.originalname.split('.').pop();
      const filename = `${uuidv4()}.${ext}`;
      const fullPath = join(folder, filename);

      writeFileSync(fullPath, file.buffer);
      urls.push(`/uploads/gallery/${projectId}/${filename}`);
    }
    return urls;
  }
}
