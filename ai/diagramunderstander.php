<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = $_POST['topic'] ?? '';
    if ($topic) {
        $prompt = "Analyze this topic and create a hierarchical structure: {$topic}. 
                  Format the response as JSON like this:
                  {
                    'topic': 'Main Topic',
                    'children': [
                      {
                        'name': 'Subtopic 1',
                        'children': []
                      }
                    ]
                  }";
        
        $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=AIzaSyAt_a5BOeb8gwlCtaolAvIQ3ycj2NxQKhU";
        $data = array(
            "contents" => array(
                array(
                    "parts" => array(
                        array("text" => $prompt)
                    )
                )
            )
        );
        
        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $content = json_decode($response, true);
            $text = $content['candidates'][0]['content']['parts'][0]['text'] ?? '';
            preg_match('/{.*}/s', $text, $matches);
            if (!empty($matches[0])) {
                $structure = json_decode($matches[0], true);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text to Diagram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function generateDiagram(data) {
            const NODE_WIDTH = 220;
            const NODE_HEIGHT = 80;
            const LEVEL_HEIGHT = 150;
            const MARGIN = 50;
            const COLORS = {
                root: {
                    bg: '#4F46E5',
                    shadow: '#3730A3'
                },
                node: {
                    bg: '#3B82F6',
                    shadow: '#1D4ED8'
                }
            };

            function createNode(x, y, text, isRoot = false) {
                const group = document.createElementNS("http://www.w3.org/2000/svg", "g");
                group.setAttribute("transform", `translate(${x}, ${y})`);

                // Shadow effect
                const shadow = document.createElementNS("http://www.w3.org/2000/svg", "rect");
                shadow.setAttribute("width", NODE_WIDTH);
                shadow.setAttribute("height", NODE_HEIGHT);
                shadow.setAttribute("rx", "16");
                shadow.setAttribute("fill", isRoot ? COLORS.root.shadow : COLORS.node.shadow);
                shadow.setAttribute("transform", "translate(4, 4)");

                // Main rectangle
                const rect = document.createElementNS("http://www.w3.org/2000/svg", "rect");
                rect.setAttribute("width", NODE_WIDTH);
                rect.setAttribute("height", NODE_HEIGHT);
                rect.setAttribute("rx", "16");
                rect.setAttribute("fill", isRoot ? COLORS.root.bg : COLORS.node.bg);
                rect.setAttribute("class", "transition-all duration-300 hover:brightness-110");

                // Text with word wrap
                const textElement = document.createElementNS("http://www.w3.org/2000/svg", "text");
                textElement.setAttribute("x", NODE_WIDTH / 2);
                textElement.setAttribute("y", NODE_HEIGHT / 2);
                textElement.setAttribute("text-anchor", "middle");
                textElement.setAttribute("class", "fill-white font-bold text-sm");

                const words = text.split(' ');
                let line = '';
                let lineHeight = 0;
                
                words.forEach((word, index) => {
                    const tspan = document.createElementNS("http://www.w3.org/2000/svg", "tspan");
                    const testLine = line + word + ' ';
                    
                    if (testLine.length > 20 && index > 0) {
                        tspan.textContent = line;
                        tspan.setAttribute("x", NODE_WIDTH / 2);
                        tspan.setAttribute("dy", lineHeight === 0 ? "-0.5em" : "1.2em");
                        textElement.appendChild(tspan);
                        line = word + ' ';
                        lineHeight++;
                    } else {
                        line = testLine;
                    }
                    
                    if (index === words.length - 1) {
                        tspan.textContent = line;
                        tspan.setAttribute("x", NODE_WIDTH / 2);
                        tspan.setAttribute("dy", lineHeight === 0 ? "-0.5em" : "1.2em");
                        textElement.appendChild(tspan);
                    }
                });

                group.appendChild(shadow);
                group.appendChild(rect);
                group.appendChild(textElement);
                return group;
            }

            function createConnection(x1, y1, x2, y2) {
                const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                const d = `M ${x1} ${y1} C ${x1} ${(y1 + y2) / 2}, ${x2} ${(y1 + y2) / 2}, ${x2} ${y2}`;
                path.setAttribute("d", d);
                path.setAttribute("class", "stroke-gray-400 transition-all duration-300");
                path.setAttribute("fill", "none");
                path.setAttribute("stroke-width", "2");
                return path;
            }

            function renderTree(svg, node, x, y, level = 0, parentX = null, parentY = null) {
                const nodeElement = createNode(x - NODE_WIDTH / 2, y, node.name || node.topic, level === 0);
                svg.appendChild(nodeElement);

                if (parentX !== null) {
                    const connection = createConnection(parentX, parentY + NODE_HEIGHT, x, y);
                    svg.insertBefore(connection, svg.firstChild);
                }

                if (node.children && node.children.length > 0) {
                    const totalWidth = NODE_WIDTH * node.children.length + MARGIN * (node.children.length - 1);
                    let startX = x - totalWidth / 2;
                    
                    node.children.forEach(child => {
                        renderTree(svg, child, startX + NODE_WIDTH / 2, y + LEVEL_HEIGHT, level + 1, x, y);
                        startX += NODE_WIDTH + MARGIN;
                    });
                }
            }

            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute("width", "100%");
            svg.setAttribute("height", "800");
            svg.setAttribute("viewBox", `0 0 ${NODE_WIDTH * 5} ${LEVEL_HEIGHT * 5}`);
            svg.setAttribute("class", "bg-gray-50 rounded-xl shadow-lg p-8");

            renderTree(svg, data, NODE_WIDTH * 2.5, MARGIN);
            return svg;
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <?php include('../component/sidebar.php'); ?>
    <main class="sm:ml-64 p-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Text to Diagram Generator
                </h1>
                <p class="mt-3 text-xl text-gray-500">
                    Enter a topic to generate a hierarchical diagram
                </p>
            </div>
    
            <div class="bg-white shadow rounded-lg p-6 mb-8">
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="topic" class="block text-sm font-medium text-gray-700">Topic</label>
                        <input type="text" name="topic" id="topic" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="e.g. Nervous System" value="<?php echo htmlspecialchars($topic ?? ''); ?>">
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Generate Diagram
                        </button>
                    </div>
                </form>
            </div>
    
            <?php if (isset($structure)): ?>
                <div class="bg-white shadow rounded-lg p-6">
                    <div id="diagram-container" class="flex justify-center">
                    </div>
                    <script>
                        const structure = <?php echo json_encode($structure); ?>;
                        const diagram = generateDiagram(structure);
                        document.getElementById('diagram-container').appendChild(diagram);
                    </script>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
