# PDF Worker - Node.js + Puppeteer for PDF generation
FROM node:20-alpine

# Install Chromium and dependencies
RUN apk add --no-cache \
    chromium \
    nss \
    freetype \
    harfbuzz \
    ca-certificates \
    ttf-freefont \
    font-noto-emoji

# Tell Puppeteer to use system Chromium
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true \
    PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser

# Create app directory
WORKDIR /app

# Create non-root user first
RUN addgroup -g 1001 nodejs && \
    adduser -S -u 1001 -G nodejs nodejs && \
    chown -R nodejs:nodejs /app

# Switch to non-root user before installing dependencies
USER nodejs

# Copy package files
COPY --chown=nodejs:nodejs package*.json ./

# Install dependencies (as nodejs user, so no chown needed)
RUN npm install --production

# Copy source
COPY --chown=nodejs:nodejs generate-pdf.js ./

# Default command (can be overridden in docker-compose)
CMD ["node", "generate-pdf.js", "--batch"]

