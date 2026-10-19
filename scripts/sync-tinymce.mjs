import { cp, mkdir, rm } from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';

const rootDir = process.cwd();
const sourceDir = path.join(rootDir, 'node_modules', 'tinymce');
const targetDir = path.join(rootDir, 'public', 'vendor', 'tinymce');

await mkdir(path.dirname(targetDir), { recursive: true });
await rm(targetDir, { recursive: true, force: true });
await cp(sourceDir, targetDir, { recursive: true });

console.log(`Synced TinyMCE assets to ${targetDir}`);
