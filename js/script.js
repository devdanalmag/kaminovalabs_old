// Three.js 3D Scene with Interactive Controls
const threeCanvas = document.getElementById('three-canvas');
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ canvas: threeCanvas, alpha: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setClearColor(0x000000, 0);

// Add OrbitControls for interactivity
const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;
controls.screenSpacePanning = false;
controls.minDistance = 5;
controls.maxDistance = 50;
controls.maxPolarAngle = Math.PI / 2;

// Create a wireframe sphere for 3D effect
const geometry = new THREE.SphereGeometry(5, 32, 32);
const material = new THREE.MeshBasicMaterial({
    color: 0x00bfff,
    wireframe: true,
    transparent: true,
    opacity: 0.3
});
const sphere = new THREE.Mesh(geometry, material);
scene.add(sphere);

// Add floating particles in 3D
const particlesGeometry = new THREE.BufferGeometry();
const particlesCount = 200;
const posArray = new Float32Array(particlesCount * 3);
for (let i = 0; i < particlesCount * 3; i++) {
    posArray[i] = (Math.random() - 0.5) * 20;
}
particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
const particlesMaterial = new THREE.PointsMaterial({
    size: 0.05,
    color: 0x4169e1
});
const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
scene.add(particlesMesh);

camera.position.z = 10;

function animateThree() {
    requestAnimationFrame(animateThree);
    sphere.rotation.x += 0.005;
    sphere.rotation.y += 0.01;
    particlesMesh.rotation.y += 0.002;
    controls.update();
    renderer.render(scene, camera);
}
animateThree();

window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

// Particle System (2D Canvas)
const canvas = document.getElementById('particles');
const ctx = canvas.getContext('2d');
canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let particles = [];
class Particle {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2 + 1;
        this.speedX = Math.random() * 0.5 - 0.25;
        this.speedY = Math.random() * 0.5 - 0.25;
        this.color = `hsl(${Math.random() * 60 + 210}, 70%, 60%)`; // Blue tones
    }
    update() {
        this.x += this.speedX;
        this.y += this.speedY;
        if (this.x > canvas.width || this.x < 0) this.speedX = -this.speedX;
        if (this.y > canvas.height || this.y < 0) this.speedY = -this.speedY;
    }
    draw() {
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

function initParticles() {
    particles = [];
    for (let i = 0; i < 100; i++) {
        particles.push(new Particle());
    }
}
function animateParticles() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => {
        p.update();
        p.draw();
    });
    requestAnimationFrame(animateParticles);
}
initParticles();
animateParticles();

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    initParticles();
});

// Mobile menu toggle
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.querySelector('.nav-links');

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    menuToggle.querySelector('i').classList.toggle('fa-bars');
    menuToggle.querySelector('i').classList.toggle('fa-times');
});

// Close mobile menu when clicking on links (except dropdown toggles)
document.querySelectorAll('.nav-links a:not(.dropdown-toggle)').forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        menuToggle.querySelector('i').classList.add('fa-bars');
        menuToggle.querySelector('i').classList.remove('fa-times');
    });
});

// Scroll reveal animation
function revealElements() {
    const elements = document.querySelectorAll('.reveal');

    elements.forEach(el => {
        const windowHeight = window.innerHeight;
        const elementTop = el.getBoundingClientRect().top;
        const elementVisible = 150;

        if (elementTop < windowHeight - elementVisible) {
            el.classList.add('active');
        }
    });
}

window.addEventListener('scroll', revealElements);
window.addEventListener('load', revealElements);

// Page loader (guard if not present)
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (!loader) return;
    setTimeout(() => {
        loader.classList.add('hidden');
    }, 1500);
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    });
});

// Header scroll effect
window.addEventListener('scroll', function () {
    const header = document.getElementById('header');
    if (!header) return;
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Animate stats counting
function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start) + "+";
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Start counter animation when stats section is in view
const statsSection = document.querySelector('.stats-container');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateValue("clients", 0, 150, 2000);
            animateValue("projects", 0, 240, 2000);
            animateValue("team", 0, 45, 2000);
            animateValue("years", 0, 8, 2000);
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

if (statsSection) {
    observer.observe(statsSection);
}

// // Project Modal Functionality
// const modal = document.getElementById('projectModal');
// const modalBody = document.getElementById('modalBody');
// const modalClose = document.getElementById('modalClose');
// const readMoreBtns = document.querySelectorAll('.read-more-btn');

// // Project details data
// const projectDetails = {
//     greentrust: {
//         title: "GreenTrust Nexus",
//         status: "Under Development",
//         category: "BioFuture & Governance",
//         description: "Climate, Trust & Tokenized Sustainability",
//         fullDescription: "A comprehensive Web3 ecosystem designed for climate tokenization, trust networks, and incentive-driven governance. GreenTrust Nexus aims to revolutionize how we approach environmental sustainability through blockchain technology, creating transparent and verifiable carbon credit systems, environmental impact tracking, and community-driven governance mechanisms.",
//         features: [
//             "Climate tokenization and carbon credit management",
//             "Trust network establishment for environmental projects",
//             "Incentive-driven governance for sustainability initiatives",
//             "Transparent environmental impact tracking",
//             "Community voting on climate projects"
//         ],
//         team: [
//             { role: "Founder", name: "Team Lead" }
//         ]
//     },
//     nodewhisper: {
//         title: "NodeWhisper",
//         status: "Under Development",
//         category: "Forensics & Security",
//         description: "Blockchain Forensics & Threat Intelligence Platform",
//         fullDescription: "Next-generation forensic tool equipped with advanced threat heatmaps, behavioral fingerprinting, and collaborative case rooms. NodeWhisper provides comprehensive blockchain analysis capabilities for law enforcement, compliance teams, and security professionals.",
//         features: [
//             "Advanced threat heatmaps and visualization",
//             "Behavioral fingerprinting for suspicious activities",
//             "Collaborative case rooms for team investigations",
//             "Real-time blockchain transaction monitoring",
//             "Comprehensive forensic reporting tools"
//         ],
//         team: [
//             { role: "Founder", name: "Team Lead" }
//         ]
//     },
//     badis: {
//         title: "BADIS (Blockchain-Agnostic DApp Immune System)",
//         status: "Novel Idea Proposal to Open Zeppelin",
//         category: "Forensics & Security",
//         description: "AI-Driven Real-Time DApp Security",
//         fullDescription: "A revolutionary cross-chain immune system that delivers automated AI threat detection and real-time patching for decentralized applications. BADIS represents a paradigm shift in DApp security, providing proactive protection across multiple blockchain networks.",
//         features: [
//             "Cross-chain compatibility and monitoring",
//             "AI-powered threat detection algorithms",
//             "Real-time automated patching system",
//             "Behavioral analysis of smart contracts",
//             "Immune system learning and adaptation"
//         ],
//         team: [
//             { role: "Founder", name: "Team Lead" }
//         ]
//     },
//     dpm: {
//         title: "DPM (Decentralized Privacy Mesh)",
//         status: "Coming Soon",
//         category: "Forensics & Security",
//         description: "Reinventing Privacy in Modular Blockchain Ecosystems",
//         fullDescription: "An innovative privacy mesh solution utilizing zero-knowledge proofs and advanced encryption techniques specifically designed for modular blockchain ecosystems. DPM ensures complete privacy while maintaining interoperability across different blockchain networks.",
//         features: [
//             "Zero-knowledge proof implementation",
//             "Advanced encryption for modular blockchains",
//             "Cross-chain privacy preservation",
//             "Decentralized privacy mesh architecture",
//             "Seamless integration with existing DApps"
//         ],
//         team: [
//             { role: "Founder", name: "Team Lead" }
//         ]
//     },
//     hashpack: {
//         title: "HashPack Logistics",
//         status: "Under Development",
//         category: "Logistics & Utility",
//         description: "Reinventing Africa's Deliveries",
//         fullDescription: "A blockchain-powered last-mile delivery solution specifically designed for the African market. HashPack Logistics provides real-time tracking, secure payments, and comprehensive trust systems for riders and vendors, revolutionizing the delivery ecosystem across Africa.",
//         features: [
//             "Real-time package tracking and monitoring",
//             "Secure blockchain-based payment system",
//             "Rider and vendor trust scoring system",
//             "Route optimization for African logistics",
//             "Mobile-first design for accessibility"
//         ],
//         team: [
//             { role: "CTO", name: "Chief Technology Officer" }
//         ]
//     },
//     hederapay: {
//         title: "HederaPay",
//         status: "Under Development",
//         category: "Finance & Payments",
//         description: "Decentralized Bill & Mobile Top-Up Manager",
//         fullDescription: "A comprehensive Web3-powered application designed for the African market that tracks airtime and data usage, enables instant top-ups with HBAR or stablecoins, and executes secure bill payments through Hedera smart contracts with immutable receipts and budgeting insights.",
//         features: [
//             "Airtime and data usage tracking",
//             "Instant top-ups with HBAR and stablecoins",
//             "Secure bill payment through smart contracts",
//             "Immutable transaction receipts",
//             "Advanced budgeting and financial insights"
//         ],
//         team: [
//             { role: "CTO", name: "Chief Technology Officer" }
//         ]
//     },
//     hashswap: {
//         title: "HashSwap",
//         status: "Under Development",
//         category: "Finance & Payments",
//         description: "DEX for Seamless Hedera Trading",
//         fullDescription: "A decentralized exchange built specifically on the Hedera network, enabling efficient token swaps, liquidity pools, and fast, low-cost transactions. HashSwap integrates the Hedera Consensus Service for complete transparency and trust in all trading activities.",
//         features: [
//             "Seamless token swapping on Hedera",
//             "Liquidity pool creation and management",
//             "Fast and low-cost transactions",
//             "Hedera Consensus Service integration",
//             "Advanced trading analytics and tools"
//         ],
//         team: [
//             { role: "CTO", name: "Chief Technology Officer" }
//         ]
//     },
//     trustfound: {
//         title: "Trustfound",
//         status: "Under Development",
//         category: "Finance & Payments",
//         description: "Decentralized Donation & Transparency Platform",
//         fullDescription: "A Hedera-powered decentralized application designed for donations including Zakat, Sadaqah, and general charity in HBAR, stablecoins, or tokens. Trustfound uses the Hedera Consensus Service for immutable tracking, smart contracts for fair fund distribution, and provides a real-time public dashboard for complete transparency.",
//         features: [
//             "Multi-currency donation support (HBAR, stablecoins, tokens)",
//             "Immutable donation tracking via Hedera Consensus Service",
//             "Smart contract-based fair fund distribution",
//             "Real-time public transparency dashboard",
//             "Zakat, Sadaqah, and charity categorization"
//         ],
//         team: [
//             { role: "CTO", name: "Chief Technology Officer" }
//         ]
//     },
//     agrochainx: {
//         title: "AgroChainX",
//         status: "Advisory & Investor",
//         category: "Ecosystem & Advisory Projects",
//         description: "Blockchain Marketplace for Farmers & Traders",
//         fullDescription: "A comprehensive mobile application enabling farmers and traders to conduct secure commercial transactions. AgroChainX features a fair-price agricultural marketplace, shipment tracking, peer-to-peer crypto exchange, and Agrobot, an AI assistant providing farming, business, and crypto insights.",
//         features: [
//             "Fair-price agricultural marketplace",
//             "Secure commercial transaction system",
//             "Real-time shipment tracking",
//             "Peer-to-peer cryptocurrency exchange",
//             "Agrobot AI assistant for farming insights"
//         ],
//         team: [
//             { role: "Advisory & Investor", name: "Strategic Advisor" }
//         ]
//     },
//     smartag: {
//         title: "Smart Agriculture Detection",
//         status: "Advisory & Investor",
//         category: "Ecosystem & Advisory Projects",
//         description: "AI-Powered Crop, Pest & Soil Analysis",
//         fullDescription: "An advanced AI solution providing comprehensive crop detection, pest and disease identification with treatment recommendations, and detailed soil composition analysis. Currently in demo mode with plans for API integration to deliver real-world agricultural intelligence to farmers and agricultural professionals.",
//         features: [
//             "AI-powered crop detection and analysis",
//             "Pest and disease identification system",
//             "Treatment recommendation engine",
//             "Comprehensive soil composition analysis",
//             "API integration for real-world deployment"
//         ],
//         team: [
//             { role: "Advisory & Investor", name: "Strategic Advisor" }
//         ]
//     },
//     safeguard: {
//         title: "Safeguard",
//         status: "Advisory",
//         category: "Ecosystem & Advisory Projects",
//         description: "SME Operating System for Africa",
//         fullDescription: "An integrated online and offline platform specifically designed to empower African small and medium enterprises. Safeguard provides comprehensive business solutions including inventory management, payment processing, e-commerce capabilities, business authentication, and credit solutions—all unified in one powerful ecosystem.",
//         features: [
//             "Comprehensive inventory management system",
//             "Integrated payment processing solutions",
//             "E-commerce platform and tools",
//             "Business authentication and verification",
//             "Credit solutions and financial services"
//         ],
//         team: [
//             { role: "Advisory", name: "Strategic Advisor" }
//         ]
//     }
// };

// // Open modal function
// function openModal(projectKey) {
//     const project = projectDetails[projectKey];
//     if (!project) return;

//     modalBody.innerHTML = `
//         <h2>${project.title}</h2>
//         <p class="modal-status"><strong>Status:</strong> ${project.status}</p>
//         <p class="modal-category"><strong>Category:</strong> ${project.category}</p>
//         <p class="modal-description"><strong>Description:</strong> ${project.description}</p>

//         <div class="modal-section">
//             <h3>Full Description</h3>
//             <p>${project.fullDescription}</p>
//         </div>

//         <div class="modal-section">
//             <h3>Key Features</h3>
//             <ul>
//                 ${project.features.map(feature => `<li>${feature}</li>`).join('')}
//             </ul>
//         </div>

//         <div class="modal-section">
//             <h3>Team</h3>
//             ${project.team.map(member => `<p><strong>${member.role}:</strong> ${member.name}</p>`).join('')}
//         </div>
//     `;

//     modal.style.display = 'block';
//     document.body.style.overflow = 'hidden';
// }

// // Close modal function
// function closeModal() {
//     modal.style.display = 'none';
//     document.body.style.overflow = 'auto';
// }

// // Event listeners for modal
// readMoreBtns.forEach(btn => {
//     btn.addEventListener('click', (e) => {
//         e.preventDefault();
//         const projectKey = btn.getAttribute('data-project');
//         openModal(projectKey);
//     });
// });

// modalClose.addEventListener('click', closeModal);

// // Close modal when clicking outside
// window.addEventListener('click', (e) => {
//     if (e.target === modal) {
//         closeModal();
//     }
// });

// // Close modal with Escape key
// document.addEventListener('keydown', (e) => {
//     if (e.key === 'Escape' && modal.style.display === 'block') {
//         closeModal();
//     }
// });

// Dropdown functionality
// const dropdown = document.querySelector('.dropdown');
// const dropdownToggle = document.querySelector('.dropdown-toggle');

// if (dropdownToggle) {
//     dropdownToggle.addEventListener('click', function (e) {
//         // Only prevent default if we're trying to show dropdown, not navigate
//         if (!dropdown.classList.contains('active')) {
//             e.preventDefault();
//             dropdown.classList.add('active');
//         } else {
//             // Allow navigation when dropdown is already open
//             dropdown.classList.remove('active');
//         }
//     });
// }

// // Close dropdown when clicking outside
// document.addEventListener('click', function (e) {
//     if (dropdown && !dropdown.contains(e.target)) {
//         dropdown.classList.remove('active');
//     }
// });

// Current section highlighting
function highlightCurrentSection() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-links a[href^="#"]');

    let currentSection = '';

    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.offsetHeight;

        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            currentSection = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('current-section');
        const href = link.getAttribute('href');
        if (href === `#${currentSection}`) {
            link.classList.add('current-section');
        }
    });
}

// Add scroll event listener for section highlighting
window.addEventListener('scroll', highlightCurrentSection);

// Initial call to highlight current section
highlightCurrentSection();

// Contact Form Handler
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const status = document.getElementById('formStatus');
        const submitBtn = this.querySelector('button[type="submit"]');
        const formData = new FormData(this);

        // Simple feedback
        submitBtn.disabled = true;
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = 'Sending... <i class="fas fa-spinner fa-spin"></i>';

        fetch('api/submit_message.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.innerHTML = `<p style="color: #00ff00; margin-top: 15px; padding: 10px; background: rgba(0, 255, 0, 0.1); border-radius: 8px;"><i class="fas fa-check-circle"></i> ${data.message}</p>`;
                    submitBtn.innerHTML = 'Sent! <i class="fas fa-check"></i>';
                    contactForm.reset();
                } else {
                    status.innerHTML = `<p style="color: #ff4a4a; margin-top: 15px; padding: 10px; background: rgba(255, 74, 74, 0.1); border-radius: 8px;"><i class="fas fa-exclamation-circle"></i> ${data.message}</p>`;
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }

                // Reset status after 5 seconds if success
                if (data.success) {
                    setTimeout(() => {
                        status.innerHTML = '';
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                status.innerHTML = '<p style="color: #ff4a4a; margin-top: 15px; padding: 10px; background: rgba(255, 74, 74, 0.1); border-radius: 8px;"><i class="fas fa-exclamation-circle"></i> An error occurred. Please try again later.</p>';
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
    });
}
