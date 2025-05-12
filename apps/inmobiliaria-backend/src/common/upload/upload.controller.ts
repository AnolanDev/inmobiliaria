import { Controller, Post, UploadedFile, UploadedFiles, UseInterceptors, Param, ParseIntPipe } from '@nestjs/common';
import { FileInterceptor, FilesInterceptor } from '@nestjs/platform-express';
import { UploadService } from './upload.service';

@Controller('api/upload')
export class UploadController {
  constructor(private readonly uploadService: UploadService) {}

  @Post('logo/:projectId')
  @UseInterceptors(FileInterceptor('file'))
  async uploadLogo(
    @UploadedFile() file: Express.Multer.File,
    @Param('projectId', ParseIntPipe) projectId: number,
  ) {
    const url = await this.uploadService.uploadLogo(
      file.buffer,
      file.originalname,
      projectId,
    );
    return { url };
  }

  @Post('gallery/:projectId')
  @UseInterceptors(FilesInterceptor('files'))
  async uploadGallery(
    @UploadedFiles() files: Express.Multer.File[],
    @Param('projectId', ParseIntPipe) projectId: number,
  ) {
    const urls = await this.uploadService.uploadGallery(
      files.map(f => ({ buffer: f.buffer, originalname: f.originalname })),
      projectId,
    );
    return { urls };
  }
}