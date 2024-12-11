//Script de canva de la luz de fondo
const canvas = document.getElementById("background-canvas");
            const ctx = canvas.getContext("2d");

            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            let lightX = Math.random() * canvas.width;
            let lightY = Math.random() * canvas.height;

            
            let speedX = (Math.random() - 0.5) * 0.5;
            let speedY = (Math.random() - 0.5) * 0.5;

            const margin = 150;

            function drawBackground() {
    
                ctx.fillStyle = "#0e0e0e";
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            
                const gradient = ctx.createRadialGradient(lightX, lightY, 0, lightX, lightY, 200);
                gradient.addColorStop(0, "rgba(255, 255, 255, 0.2)");
                gradient.addColorStop(1, "rgba(255, 255, 255, 0)");
            
                ctx.beginPath();
                ctx.fillStyle = gradient;
                ctx.arc(lightX, lightY, 200, 0, Math.PI * 2);
                ctx.fill();
            
                lightX += speedX;
                lightY += speedY;
            
                if (lightX < margin || lightX > canvas.width - margin) speedX *= -1;
                if (lightY < margin || lightY > canvas.height - margin) speedY *= -1;
            
                requestAnimationFrame(drawBackground);
            }
            drawBackground();

            window.addEventListener("resize", () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
