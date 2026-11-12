import 'package:flutter_test/flutter_test.dart';
import 'package:biblia_show/main.dart';

void main() {
  testWidgets('Bíblia Show app smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const BibliaShowApp());
    expect(find.text('Bíblia Show'), findsWidgets);
  });
}
