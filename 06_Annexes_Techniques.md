# 06. Annexes Techniques - ChatApp

---

## Annexe A : Schéma de Base de Données (Prisma)

Le schéma suivant a été optimisé pour PostgreSQL.

```prisma
datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

generator client {
  provider = "prisma-client-js"
}

model User {
  id       Int       @id @default(autoincrement())
  username String    @unique
  password String?
  avatar   String?
  messages Message[]
}

model Message {
  id        Int      @id @default(autoincrement())
  role      String
  content   String
  createdAt DateTime @default(now())
  user      User?    @relation(fields: [userId], references: [id])
}
```

---

## Annexe B : Configuration Docker

### Dockerfile (Backend)
```dockerfile
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npx prisma generate
EXPOSE 3000
CMD ["npm", "run", "dev"]
```

### Docker-Compose
```yaml
services:
  app:
    build: .
    ports:
      - "3000:3000"
    environment:
      - DATABASE_URL=postgresql://user:pass@db:5432/chatdb
  db:
    image: postgres:15
    environment:
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=pass
      - POSTGRES_DB=chatdb
```

---

## Annexe C : Scripts de Test et Logs

### Journal de Test (Simulation)
```text
> chat-web-ai@0.1.0 test
> node --test backend/services/chatService.test.js

✔ getMessages should return an array of messages (5ms)
✔ createMessage should return the created message object (12ms)
✔ getAIResponse should simulated delay (105ms)

tests 3
pass 3
fail 0
cancelled 0
skipped 0
todo 0
duration_ms 142.5
```

---

## Annexe D : Bibliographie et Ressources
*   *Documentation Next.js (App Router)* : https://nextjs.org/docs
*   *Documentation Prisma* : https://www.prisma.io/docs
*   *Groq Cloud Console* : https://console.groq.com/
*   *UML Modeling Standards* : https://www.omg.org/spec/UML/
